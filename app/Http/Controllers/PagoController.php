<?php

namespace App\Http\Controllers;

use App\Services\MercadoPagoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class PagoController
{
    public function mercadoPagoReturn(Request $request, MercadoPagoService $mercadoPago): RedirectResponse
    {
        try {
            $result = $mercadoPago->syncReturn($request->query());

            if (($result['status'] ?? '') === 'approved') {
                return redirect('/dashboard')->with('status', 'Pago confirmado. Funciones activadas.');
            }

            return redirect('/suscripcion/planes')->with('status', 'Pago recibido como pendiente. Mercado Pago confirmara el estado en unos minutos.');
        } catch (Throwable $exception) {
            report($exception);

            return redirect('/suscripcion/planes')->with('status', 'No fue posible confirmar el pago todavia. Intentaremos validarlo con el webhook.');
        }
    }

    public function mercadoPagoWebhook(Request $request, MercadoPagoService $mercadoPago): JsonResponse
    {
        try {
            $result = $mercadoPago->handleWebhook(
                $request->all(),
                $request->query(),
                $request->headers->all()
            );

            return response()->json($result, ! empty($result['ok']) ? 200 : 400);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json(['ok' => false], 400);
        }
    }
}
