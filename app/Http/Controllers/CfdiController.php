<?php

namespace App\Http\Controllers;

use App\Models\Cfdi;
use App\Rules\RfcValido;
use App\Support\MembershipAccess;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CfdiController
{
    public function index(Request $request): View
    {
        $user = $request->user()->loadMissing('profile');
        $periodStart = now()->startOfMonth();
        $periodEnd = now()->endOfMonth();
        $canUseMultipleRfcs = MembershipAccess::canUseMultipleRfcs($user);
        $profileRfc = (string) $user->profile?->rfc;

        return view('dashboard.cfdi.lista', [
            'cfdis' => collect(),
            'metrics' => $this->monthlyMetrics((int) $user->id, $periodStart, $periodEnd),
            'downloadHistory' => $this->downloadHistory((int) $user->id),
            'canExportExcel' => true,
            'canUseMultipleRfcs' => $canUseMultipleRfcs,
            'profile' => $user->profile,
            'showUpgradePrompt' => $this->shouldShowUpgradePrompt((int) $user->id, $canUseMultipleRfcs),
            'filters' => [
                'tipo' => $request->query('tipo', 'todos'),
                'fecha_inicio' => $request->query('fecha_inicio', $periodStart->toDateString()),
                'fecha_fin' => $request->query('fecha_fin', $periodEnd->toDateString()),
                'rfc' => $canUseMultipleRfcs ? $request->query('rfc', '') : $profileRfc,
            ],
        ]);
    }

    public function export(Request $request): StreamedResponse|RedirectResponse
    {
        $user = $request->user()->loadMissing('profile');
        $canUseMultipleRfcs = MembershipAccess::canUseMultipleRfcs($user);
        $filters = $request->validate([
            'tipo' => ['nullable', 'in:todos,emitidos,recibidos'],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'rfc' => ['nullable', 'string', 'min:12', 'max:13', new RfcValido()],
        ]);

        $start = CarbonImmutable::parse($filters['fecha_inicio'] ?? now()->startOfMonth()->toDateString())->startOfDay();
        $end = CarbonImmutable::parse($filters['fecha_fin'] ?? now()->endOfMonth()->toDateString())->endOfDay();
        $rfc = mb_strtoupper((string) ($filters['rfc'] ?? $user->profile?->rfc), 'UTF-8');

        if (! $canUseMultipleRfcs) {
            if (! MembershipAccess::isOwnProfileRfc($user, $rfc) || ! $this->isFreeMonthlyRange($start, $end)) {
                return redirect('/suscripcion/upgrade')->with('status', 'El modo gratuito exporta solo el RFC de tu perfil y maximo 1 mes dentro del ano en curso.');
            }
        }

        return response()->streamDownload(function () use ($user, $filters, $start, $end, $rfc): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['UUID', 'Tipo', 'RFC emisor', 'Emisor', 'RFC receptor', 'Receptor', 'Fecha', 'Subtotal', 'IVA', 'Total', 'Estatus']);

            if (Schema::hasTable('cfdis')) {
                $query = DB::table('cfdis')
                    ->where('user_id', $user->id)
                    ->whereBetween('fecha_emision', [$start, $end]);

                if (($filters['tipo'] ?? 'todos') !== 'todos') {
                    $query->where('tipo', $filters['tipo']);
                }

                $query->where(function ($query) use ($rfc): void {
                    $query->where('rfc_emisor', $rfc)
                        ->orWhere('rfc_receptor', $rfc);
                });

                $query->orderByDesc('fecha_emision')->chunk(200, function ($rows) use ($handle): void {
                    foreach ($rows as $row) {
                        fputcsv($handle, [
                            $row->uuid,
                            $row->tipo,
                            $row->rfc_emisor,
                            $row->nombre_emisor,
                            $row->rfc_receptor,
                            $row->nombre_receptor,
                            $row->fecha_emision,
                            $row->subtotal,
                            $row->iva,
                            $row->total,
                            $row->estatus,
                        ]);
                    }
                });
            }

            fclose($handle);
        }, 'cfdi-export.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function show(Cfdi $cfdi): View
    {
        return view('dashboard.cfdi.detalle', [
            'cfdi' => $cfdi,
        ]);
    }

    private function monthlyMetrics(int $userId, mixed $periodStart, mixed $periodEnd): array
    {
        $empty = [
            'emitidos_count' => 0,
            'emitidos_total' => 0.0,
            'iva_trasladado' => 0.0,
            'recibidos_count' => 0,
            'recibidos_total' => 0.0,
            'iva_acreditable' => 0.0,
        ];

        if (! Schema::hasTable('cfdis')) {
            return $empty;
        }

        $base = DB::table('cfdis')
            ->where('user_id', $userId)
            ->whereBetween('fecha_emision', [$periodStart, $periodEnd]);

        $emitidos = (clone $base)->where('tipo', 'emitidos');
        $recibidos = (clone $base)->where('tipo', 'recibidos');

        return [
            'emitidos_count' => (int) $emitidos->count(),
            'emitidos_total' => (float) (clone $emitidos)->sum('total'),
            'iva_trasladado' => (float) (clone $emitidos)->sum('iva'),
            'recibidos_count' => (int) $recibidos->count(),
            'recibidos_total' => (float) (clone $recibidos)->sum('total'),
            'iva_acreditable' => (float) (clone $recibidos)->sum('iva'),
        ];
    }

    private function isFreeMonthlyRange(CarbonImmutable $start, CarbonImmutable $end): bool
    {
        $currentYear = now()->year;

        if ((int) $start->year !== $currentYear || (int) $end->year !== $currentYear) {
            return false;
        }

        return $end->startOfDay()->lessThanOrEqualTo($start->startOfDay()->addMonthNoOverflow());
    }

    private function shouldShowUpgradePrompt(int $userId, bool $canUseMultipleRfcs): bool
    {
        if ($canUseMultipleRfcs || ! Schema::hasTable('descarga_jobs')) {
            return false;
        }

        return DB::table('descarga_jobs')->where('user_id', $userId)->count() >= 2;
    }

    private function downloadHistory(int $userId): mixed
    {
        if (! Schema::hasTable('descarga_jobs')) {
            return collect();
        }

        $query = DB::table('descarga_jobs')
            ->leftJoin('rfcs', 'rfcs.id', '=', 'descarga_jobs.rfc_id')
            ->where('descarga_jobs.user_id', $userId)
            ->select([
                'descarga_jobs.id',
                'descarga_jobs.tipo',
                'descarga_jobs.estado',
                'descarga_jobs.fecha_inicio',
                'descarga_jobs.fecha_fin',
                'descarga_jobs.solicitud_id',
                'descarga_jobs.total_cfdi',
                'descarga_jobs.created_at',
                'rfcs.rfc',
            ])
            ->orderByDesc('descarga_jobs.created_at')
            ->limit(10);

        return $query->get();
    }
}
