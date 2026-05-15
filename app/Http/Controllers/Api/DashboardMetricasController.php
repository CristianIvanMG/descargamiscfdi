<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;

class DashboardMetricasController
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'cfdi_emitidos' => 0,
            'cfdi_recibidos' => 0,
            'iva_trasladado' => 0,
            'iva_acreditable' => 0,
        ]);
    }
}
