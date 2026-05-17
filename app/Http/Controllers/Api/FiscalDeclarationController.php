<?php

namespace App\Http\Controllers\Api;

use App\Services\Fiscal\FiscalAutomationEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FiscalDeclarationController
{
    public function __invoke(Request $request, FiscalAutomationEngine $engine): JsonResponse
    {
        $apiKey = (string) config('services.fiscal_api.key');

        if ($apiKey !== '' && ! hash_equals($apiKey, (string) $request->header('X-ContaPro-Api-Key'))) {
            return response()->json(['message' => 'No autorizado.'], 401);
        }

        $validated = $request->validate([
            'cfdi_emitidos' => ['present', 'array'],
            'cfdi_recibidos' => ['present', 'array'],
            'movimientos_bancarios' => ['present', 'array'],
            'regimen' => ['required', Rule::in(['RESICO', 'ACTIVIDAD_EMPRESARIAL', 'HONORARIOS'])],
            'periodo' => ['required', 'date_format:Y-m'],
            'datos_extra' => ['sometimes', 'array'],
        ]);

        return response()->json($engine->process($validated));
    }
}
