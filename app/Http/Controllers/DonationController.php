<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DonationController
{
    public function mercadoPago(Request $request): RedirectResponse
    {
        if (Schema::hasTable('donations')) {
            DB::table('donations')->insert([
                'user_id' => $request->user()->id,
                'proveedor_pago' => 'mercadopago',
                'estado' => 'pendiente',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $checkoutUrl = config('services.mercadopago.checkout_url');

        if (! is_string($checkoutUrl) || $checkoutUrl === '') {
            return back()->with('status', 'Mercado Pago todavía no está configurado.');
        }

        return redirect()->away($checkoutUrl);
    }
}
