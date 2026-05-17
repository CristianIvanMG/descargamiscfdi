<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class MercadoPagoService
{
    public const PLANS = [
        'mensual' => ['name' => 'Mensual', 'price' => 99.0, 'months' => 1],
        'anual_basico' => ['name' => 'Anual basico', 'price' => 199.0, 'months' => 12],
        'anual_completo' => ['name' => 'Anual completo', 'price' => 399.0, 'months' => 12],
    ];

    public static function publicPlans(): array
    {
        return [
            [
                'clave' => 'gratis',
                'nombre' => 'Gratis',
                'precio' => 0,
                'periodo' => 'sin pago',
                'descripcion' => 'Para probar CFDI con tu propio RFC.',
                'beneficios' => [
                    'RFC del perfil bloqueado',
                    'Solicitudes por mes del año en curso',
                    'Exportacion a Excel del RFC propio',
                    'Validacion SAT con e.firma',
                ],
                'cta' => 'Empezar ahora',
                'popular' => false,
            ],
            [
                'clave' => 'mensual',
                'nombre' => 'Mensual',
                'precio' => 99,
                'periodo' => 'MXN / mes',
                'descripcion' => 'Para contadores que necesitan operar mas rapido.',
                'beneficios' => [
                    'Clientes y multiples RFC',
                    'Mayor volumen operativo',
                    'Exportacion avanzada',
                    'Soporte para flujo contable mensual',
                ],
                'cta' => 'Suscribirme',
                'popular' => false,
            ],
            [
                'clave' => 'anual_basico',
                'nombre' => 'Anual basico',
                'precio' => 199,
                'periodo' => 'MXN / año',
                'descripcion' => 'Ideal para uso constante con ahorro anual.',
                'beneficios' => [
                    'Todo lo del plan mensual',
                    'Mejor costo anual',
                    'Gestion de clientes',
                    'Descargas avanzadas',
                ],
                'cta' => 'Suscribirme',
                'popular' => true,
            ],
            [
                'clave' => 'anual_completo',
                'nombre' => 'Anual completo',
                'precio' => 399,
                'periodo' => 'MXN / año',
                'descripcion' => 'Para despachos que requieren control e historial.',
                'beneficios' => [
                    'Historial SAT completo',
                    'Multiples RFC y clientes',
                    'Exportacion y auditoria operativa',
                    'Preparado para reportes avanzados',
                ],
                'cta' => 'Actualizar',
                'popular' => false,
            ],
        ];
    }

    public function createDonationPreference(User $user): string
    {
        $amount = (float) config('services.mercadopago.donation_amount', 50);
        $paymentId = $this->createPayment($user->id, 'donacion', null, $amount);

        return $this->createPreference($paymentId, $user, 'Donacion - Plataforma CFDI', $amount);
    }

    public function createSubscriptionPreference(User $user, string $plan): string
    {
        if (! array_key_exists($plan, self::PLANS)) {
            throw new RuntimeException('Plan no valido.');
        }

        $active = $this->activeSubscription((int) $user->id);

        if ($active && $active->plan === $plan) {
            throw new RuntimeException('Ya tienes este plan activo.');
        }

        $definition = self::PLANS[$plan];
        $paymentId = $this->createPayment($user->id, 'suscripcion', $plan, (float) $definition['price']);

        return $this->createPreference($paymentId, $user, 'Suscripcion CFDI - '.$definition['name'], (float) $definition['price']);
    }

    public function handleWebhook(array $payload, array $query, array $headers): array
    {
        if (! $this->hasValidSignature($query, $headers)) {
            return ['ok' => false, 'error' => 'Firma invalida.'];
        }

        $type = (string) ($payload['type'] ?? $payload['topic'] ?? $query['type'] ?? $query['topic'] ?? '');
        $paymentId = (string) ($payload['data']['id'] ?? $payload['id'] ?? $query['data.id'] ?? $query['id'] ?? '');

        if ($type !== 'payment' || $paymentId === '') {
            return ['ok' => true, 'ignored' => true];
        }

        $payment = $this->request('GET', '/v1/payments/'.rawurlencode($paymentId));
        $this->applyPayment($payment);

        return ['ok' => true];
    }

    public function syncReturn(array $query): array
    {
        $paymentId = (string) ($query['payment_id'] ?? $query['collection_id'] ?? $query['data.id'] ?? $query['id'] ?? '');

        if ($paymentId === '' || $paymentId === 'null') {
            return ['ok' => false, 'pending' => true];
        }

        $payment = $this->request('GET', '/v1/payments/'.rawurlencode($paymentId));
        $this->applyPayment($payment);

        return ['ok' => true, 'status' => (string) ($payment['status'] ?? 'pending')];
    }

    private function createPreference(int $paymentId, User $user, string $title, float $amount): string
    {
        $externalReference = 'cfdi-payment-'.$paymentId;
        $payload = [
            'items' => [[
                'id' => (string) $paymentId,
                'title' => $title,
                'quantity' => 1,
                'currency_id' => 'MXN',
                'unit_price' => round($amount, 2),
            ]],
            'payer' => [
                'email' => $user->email,
                'name' => $user->name,
            ],
            'external_reference' => $externalReference,
            'notification_url' => url('/webhooks/mercadopago'),
            'back_urls' => [
                'success' => url('/pagos/mercadopago/retorno?result=success'),
                'failure' => url('/pagos/mercadopago/retorno?result=failure'),
                'pending' => url('/pagos/mercadopago/retorno?result=pending'),
            ],
            'auto_return' => 'approved',
            'statement_descriptor' => 'CONTAPRO',
            'metadata' => [
                'payment_id' => $paymentId,
                'user_id' => $user->id,
            ],
        ];

        $body = $this->request('POST', '/checkout/preferences', $payload);
        $checkoutUrl = (string) ($body['init_point'] ?? $body['sandbox_init_point'] ?? '');

        if ($checkoutUrl === '') {
            throw new RuntimeException('Mercado Pago no devolvio URL de pago.');
        }

        DB::table('pagos')->where('id', $paymentId)->update([
            'external_reference' => $externalReference,
            'provider_preference_id' => (string) ($body['id'] ?? ''),
            'updated_at' => now(),
        ]);

        return $checkoutUrl;
    }

    private function createPayment(int $userId, string $type, ?string $plan, float $amount): int
    {
        if (! Schema::hasTable('pagos')) {
            throw new RuntimeException('Falta crear la tabla pagos.');
        }

        return (int) DB::table('pagos')->insertGetId([
            'user_id' => $userId,
            'tipo_pago' => $type,
            'plan' => $plan,
            'monto' => $amount,
            'estado' => 'pendiente',
            'proveedor_pago' => 'mercadopago',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function applyPayment(array $mpPayment): void
    {
        $externalReference = (string) ($mpPayment['external_reference'] ?? '');
        $providerPaymentId = (string) ($mpPayment['id'] ?? '');
        $status = (string) ($mpPayment['status'] ?? '');

        if ($externalReference === '' || $providerPaymentId === '' || ! str_starts_with($externalReference, 'cfdi-payment-')) {
            throw new RuntimeException('Pago sin referencia valida.');
        }

        $paymentId = (int) str_replace('cfdi-payment-', '', $externalReference);
        $payment = DB::table('pagos')->where('id', $paymentId)->first();

        if (! $payment) {
            throw new RuntimeException('Pago local no encontrado.');
        }

        if ($payment->estado === 'aprobado') {
            return;
        }

        $normalized = match ($status) {
            'approved' => 'aprobado',
            'rejected' => 'rechazado',
            'cancelled' => 'cancelado',
            default => 'pendiente',
        };

        $paymentUpdate = [
            'provider_payment_id' => $providerPaymentId,
            'estado' => $normalized,
            'payload' => json_encode($mpPayment, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'updated_at' => now(),
        ];

        if ($normalized === 'aprobado') {
            $paymentUpdate['fecha_inicio'] = now();
            $paymentUpdate['fecha_fin'] = $payment->tipo_pago === 'donacion'
                ? now()->addYear()
                : now()->addMonths((int) (self::PLANS[$payment->plan]['months'] ?? 1));
        }

        DB::table('pagos')->where('id', $paymentId)->update($paymentUpdate);

        if ($normalized !== 'aprobado') {
            return;
        }

        if ($payment->tipo_pago === 'donacion') {
            $this->activateDonation((int) $payment->user_id, (float) $payment->monto, $providerPaymentId);
            return;
        }

        if ($payment->tipo_pago === 'suscripcion' && is_string($payment->plan)) {
            $this->activateSubscription((int) $payment->user_id, $payment->plan, $providerPaymentId);
        }
    }

    private function activateDonation(int $userId, float $amount, string $providerPaymentId): void
    {
        if (! Schema::hasTable('donations')) {
            return;
        }

        DB::table('donations')->insert([
            'user_id' => $userId,
            'proveedor_pago' => 'mercadopago',
            'proveedor_id' => $providerPaymentId,
            'estado' => 'aprobada',
            'monto' => $amount,
            'donated_at' => now(),
            'active_until' => now()->addYear(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function activateSubscription(int $userId, string $plan, string $providerPaymentId): void
    {
        if (! Schema::hasTable('suscripciones') || ! array_key_exists($plan, self::PLANS)) {
            return;
        }

        $definition = self::PLANS[$plan];

        $expiresAt = now()->addMonths((int) $definition['months']);

        $replacement = [
            'estatus' => 'reemplazada',
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('suscripciones', 'activo')) {
            $replacement['activo'] = false;
        }

        DB::table('suscripciones')
            ->where('user_id', $userId)
            ->whereIn('estatus', ['activa', 'activo', 'active', 'paid'])
            ->update($replacement);

        $payload = [
            'user_id' => $userId,
            'plan' => $plan,
            'proveedor_pago' => 'mercadopago',
            'proveedor_id' => $providerPaymentId,
            'estatus' => 'activa',
            'periodo_inicio' => now(),
            'periodo_fin' => $expiresAt,
            'renovacion_automatica' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('suscripciones', 'tipo_plan')) {
            $payload['tipo_plan'] = $plan;
        }

        if (Schema::hasColumn('suscripciones', 'activo')) {
            $payload['activo'] = true;
        }

        if (Schema::hasColumn('suscripciones', 'fecha_vencimiento')) {
            $payload['fecha_vencimiento'] = $expiresAt;
        }

        DB::table('suscripciones')->insert($payload);

        $this->sendSubscriptionEmail($userId, $plan, $expiresAt);
    }

    private function activeSubscription(int $userId): ?object
    {
        if (! Schema::hasTable('suscripciones')) {
            return null;
        }

        return DB::table('suscripciones')
            ->where('user_id', $userId)
            ->whereIn('estatus', ['activa', 'activo', 'active', 'paid'])
            ->where(function ($query): void {
                $query->whereNull('periodo_fin')
                    ->orWhere('periodo_fin', '>', now());
            })
            ->orderByDesc('created_at')
            ->first();
    }

    private function sendSubscriptionEmail(int $userId, string $plan, mixed $expiresAt): void
    {
        $user = User::query()->find($userId);

        if (! $user) {
            return;
        }

        try {
            Mail::raw(
                "Pago confirmado. Tu suscripcion ContaPro ({$plan}) esta activa hasta ".$expiresAt->format('d/m/Y').'.',
                fn ($message) => $message->to($user->email)->subject('Suscripcion ContaPro activa')
            );
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    private function request(string $method, string $path, ?array $payload = null): array
    {
        $token = (string) config('services.mercadopago.access_token');

        if ($token === '') {
            throw new RuntimeException('Mercado Pago no esta configurado.');
        }

        $response = Http::withToken($token)
            ->acceptJson()
            ->asJson()
            ->timeout(20)
            ->send($method, rtrim((string) config('services.mercadopago.base_url'), '/').$path, $payload === null ? [] : [
                'json' => $payload,
            ]);

        if (! $response->successful()) {
            throw new RuntimeException((string) ($response->json('message') ?: 'Mercado Pago rechazo la solicitud.'));
        }

        return $response->json() ?: [];
    }

    private function hasValidSignature(array $query, array $headers): bool
    {
        $secret = (string) config('services.mercadopago.webhook_secret');

        if ($secret === '') {
            return true;
        }

        $signature = (string) ($headers['x-signature'][0] ?? $headers['X-Signature'][0] ?? $headers['x-signature'] ?? $headers['X-Signature'] ?? '');
        $requestId = (string) ($headers['x-request-id'][0] ?? $headers['X-Request-Id'][0] ?? $headers['x-request-id'] ?? $headers['X-Request-Id'] ?? '');

        if ($signature === '' || $requestId === '') {
            return false;
        }

        $parts = [];
        foreach (explode(',', $signature) as $part) {
            [$key, $value] = array_pad(explode('=', trim($part), 2), 2, '');
            $parts[$key] = $value;
        }

        $timestamp = (string) ($parts['ts'] ?? '');
        $hash = (string) ($parts['v1'] ?? '');
        $dataId = strtolower((string) ($query['data.id'] ?? $query['id'] ?? ''));

        if ($timestamp === '' || $hash === '' || $dataId === '') {
            return false;
        }

        $manifest = 'id:'.$dataId.';request-id:'.$requestId.';ts:'.$timestamp.';';

        return hash_equals(hash_hmac('sha256', $manifest, $secret), $hash);
    }
}
