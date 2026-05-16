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
                ['clave' => 'mensual', 'nombre' => 'Mensual', 'precio' => 99],
                ['clave' => 'anual_promo', 'nombre' => 'Anual promoción', 'precio' => 199],
                ['clave' => 'anual', 'nombre' => 'Anual completa', 'precio' => 399],
            ],
        ]);
    }

    public function checkout(): RedirectResponse
    {
        return back()->with('status', __('app.suscripcion.pending_payments'));
    }
}
