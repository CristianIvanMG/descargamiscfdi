<?php

namespace App\Http\Controllers;

use App\Services\MercadoPagoService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Throwable;

class SuscripcionController
{
    public function plans(Request $request): View
    {
        return view('suscripcion.planes', [
            'planes' => MercadoPagoService::publicPlans(),
            'currentSubscription' => $request->user() ? $this->currentSubscription((int) $request->user()->id) : null,
        ]);
    }

    public function account(Request $request): View
    {
        return view('suscripcion.account', [
            'planes' => MercadoPagoService::publicPlans(),
            'currentSubscription' => $this->currentSubscription((int) $request->user()->id),
            'payments' => $this->paymentHistory((int) $request->user()->id),
        ]);
    }

    public function checkout(Request $request, MercadoPagoService $mercadoPago): RedirectResponse
    {
        return $this->create($request, $mercadoPago);
    }

    public function create(Request $request, MercadoPagoService $mercadoPago): RedirectResponse
    {
        $validated = $request->validate([
            'plan' => ['required', Rule::in(array_keys(MercadoPagoService::PLANS))],
        ]);

        $current = $this->currentSubscription((int) $request->user()->id);

        if ($current && $current->plan === $validated['plan']) {
            return redirect('/perfil/suscripcion')->with('status', 'Ya tienes este plan activo.');
        }

        try {
            return redirect()->away($mercadoPago->createSubscriptionPreference($request->user(), $validated['plan']));
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('status', 'No fue posible iniciar Mercado Pago. Revisa la configuracion de pagos.');
        }
    }

    public function change(Request $request, MercadoPagoService $mercadoPago): RedirectResponse
    {
        return $this->create($request, $mercadoPago);
    }

    public function cancel(Request $request): RedirectResponse
    {
        if (! Schema::hasTable('suscripciones')) {
            return back()->with('status', 'No hay tabla de suscripciones activa.');
        }

        $subscription = $this->currentSubscription((int) $request->user()->id);

        if (! $subscription) {
            return back()->with('status', 'No tienes una suscripcion activa.');
        }

        $payload = [
            'estatus' => 'cancelada',
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('suscripciones', 'activo')) {
            $payload['activo'] = false;
        }

        DB::table('suscripciones')->where('id', $subscription->id)->update($payload);

        return back()->with('status', 'Suscripcion cancelada. Mantendras acceso hasta la fecha de vencimiento registrada.');
    }

    public function current(Request $request): JsonResponse
    {
        return response()->json([
            'subscription' => $this->currentSubscription((int) $request->user()->id),
        ]);
    }

    public function plansJson(): JsonResponse
    {
        return response()->json([
            'plans' => MercadoPagoService::publicPlans(),
        ]);
    }

    private function currentSubscription(int $userId): ?object
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
            ->orderByDesc('periodo_fin')
            ->orderByDesc('created_at')
            ->first();
    }

    private function paymentHistory(int $userId): mixed
    {
        if (! Schema::hasTable('pagos')) {
            return collect();
        }

        return DB::table('pagos')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();
    }
}
