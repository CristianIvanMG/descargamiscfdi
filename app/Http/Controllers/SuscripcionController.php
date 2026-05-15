<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SuscripcionController
{
    public function plans(): View
    {
        return view('suscripcion.planes', [
            'planes' => [
                ['clave' => 'gratis', 'nombre' => __('app.plans.free'), 'precio' => 0],
                ['clave' => 'pro', 'nombre' => __('app.plans.pro'), 'precio' => 449],
                ['clave' => 'despacho', 'nombre' => __('app.plans.firm'), 'precio' => 1299],
            ],
        ]);
    }

    public function checkout(): RedirectResponse
    {
        return back()->with('status', __('app.suscripcion.pending_payments'));
    }
}
