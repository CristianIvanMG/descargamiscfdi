<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class DashboardController
{
    public function __invoke(): View
    {
        return view('dashboard.index', [
            'metricas' => [
                'cfdi_emitidos' => 0,
                'cfdi_recibidos' => 0,
                'iva_trasladado' => 0,
                'iva_acreditable' => 0,
            ],
        ]);
    }
}
