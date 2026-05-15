<?php

namespace App\Services;

use RuntimeException;

class SatWebService
{
    public function solicitarDescarga(array $payload): string
    {
        throw new RuntimeException('SAT Web Service se implementa en la fase 6.');
    }

    public function verificarSolicitud(string $solicitudId): array
    {
        throw new RuntimeException('Verificacion SAT se implementa en la fase 6.');
    }

    public function descargarPaquete(string $paqueteId): string
    {
        throw new RuntimeException('Descarga SAT se implementa en la fase 6.');
    }
}
