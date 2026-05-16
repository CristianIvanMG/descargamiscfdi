<?php

namespace App\Http\Controllers;

use App\Support\MembershipAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController
{
    public function __invoke(Request $request): View
    {
        $user = $request->user()->loadMissing('profile');
        $profile = $user->profile;
        $periodStart = now()->startOfMonth();
        $periodEnd = now()->endOfMonth();
        $metrics = $this->monthlyMetrics($user->id, $periodStart, $periodEnd);

        return view('dashboard.index', [
            'profile' => $profile,
            'periodLabel' => $periodStart->translatedFormat('F Y'),
            'canUseMultipleRfcs' => MembershipAccess::canUseMultipleRfcs($user),
            'metrics' => $metrics,
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
}
