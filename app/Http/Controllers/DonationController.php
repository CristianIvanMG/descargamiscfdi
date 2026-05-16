<?php

namespace App\Http\Controllers;

use App\Services\MercadoPagoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class DonationController
{
    public function mercadoPago(Request $request, MercadoPagoService $mercadoPago): RedirectResponse
    {
        try {
            return redirect()->away($mercadoPago->createDonationPreference($request->user()));
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('status', 'No fue posible iniciar Mercado Pago. Revisa la configuracion de pagos.');
        }
    }
}
