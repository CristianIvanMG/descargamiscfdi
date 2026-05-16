<?php

namespace App\Http\Controllers;

use App\Services\SatAuthenticationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class SatAuthenticationController
{
    public function store(Request $request, SatAuthenticationService $satAuthentication): JsonResponse
    {
        $validated = $request->validate([
            'cer' => ['required', 'file', 'max:512'],
            'key' => ['required', 'file', 'max:512'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        try {
            $token = $satAuthentication->authenticate(
                cerPath: $validated['cer']->getRealPath(),
                keyPath: $validated['key']->getRealPath(),
                password: $validated['password'],
            );

            $request->session()->put('sat_authenticated', true);
            $request->session()->put('sat_authenticated_at', now()->toIso8601String());
            $request->session()->put('sat_token', $token);

            return response()->json([
                'ok' => true,
                'message' => 'Conexión con el SAT validada correctamente',
            ]);
        } catch (Throwable $exception) {
            Log::warning('No fue posible autenticar e.firma contra SAT', [
                'user_id' => $request->user()?->getKey(),
                'exception' => $exception->getMessage(),
            ]);

            $request->session()->forget(['sat_authenticated', 'sat_authenticated_at', 'sat_token']);

            return response()->json([
                'ok' => false,
                'message' => 'No fue posible validar la e.firma. Verifica tus archivos y contraseña.',
            ], 422);
        }
    }
}
