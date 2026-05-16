<?php

namespace App\Http\Controllers;

use App\Services\MercadoPagoService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class SuscripcionController
{
    public function plans(): View
    {
        return view('suscripcion.planes', [
            'planes' => [
                ['clave' => 'gratis', 'nombre' => __('app.plans.free'), 'precio' => 0],
                ['clave' => 'mensual', 'nombre' => 'Mensual', 'precio' => 99],
                ['clave' => 'anual_basico', 'nombre' => 'Anual basico', 'precio' => 199],
                ['clave' => 'anual_completo', 'nombre' => 'Anual completo', 'precio' => 399],
            ],
        ]);
    }

    public function checkout(Request $request, MercadoPagoService $mercadoPago): RedirectResponse
    {
        $validated = $request->validate([
            'plan' => ['required', 'in:mensual,anual_basico,anual_completo'],
        ]);

        try {
            return redirect()->away($mercadoPago->createSubscriptionPreference($request->user(), $validated['plan']));
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('status', 'No fue posible iniciar Mercado Pago. Revisa la configuracion de pagos.');
        }
    }
}
