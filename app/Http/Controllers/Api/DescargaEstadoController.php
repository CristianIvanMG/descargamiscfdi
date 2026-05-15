<?php

namespace App\Http\Controllers\Api;

use App\Models\DescargaJob;
use Illuminate\Http\JsonResponse;

class DescargaEstadoController
{
    public function __invoke(DescargaJob $descargaJob): JsonResponse
    {
        return response()->json([
            'id' => $descargaJob->getKey(),
            'estado' => $descargaJob->estado?->value,
            'total_cfdi' => $descargaJob->total_cfdi,
            'mensaje_error' => $descargaJob->mensaje_error,
        ]);
    }
}
