<?php

namespace App\Http\Controllers;

use App\Models\Cfdi;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CfdiController
{
    public function index(Request $request): View
    {
        $periodStart = now()->startOfMonth();
        $periodEnd = now()->endOfMonth();

        return view('dashboard.cfdi.lista', [
            'cfdis' => collect(),
            'metrics' => $this->monthlyMetrics((int) $request->user()->id, $periodStart, $periodEnd),
            'filters' => [
                'tipo' => $request->query('tipo', 'todos'),
                'fecha_inicio' => $request->query('fecha_inicio', $periodStart->toDateString()),
                'fecha_fin' => $request->query('fecha_fin', $periodEnd->toDateString()),
                'rfc' => $request->query('rfc', ''),
            ],
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
}
