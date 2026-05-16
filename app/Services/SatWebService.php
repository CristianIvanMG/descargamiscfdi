<?php

namespace App\Services;

use RuntimeException;

class SatWebService
{
    public function solicitarDescarga(array $payload): string
    {
        throw new RuntimeException('La solicitud SOAP al SAT requiere el request firmado con la librería phpcfdi en el servidor.');
    }

    public function verificarSolicitud(string $solicitudId): array
    {
        throw new RuntimeException('La verificación SOAP al SAT requiere el request firmado con la librería phpcfdi en el servidor.');
    }

    public function descargarPaquete(string $paqueteId): string
    {
        throw new RuntimeException('La descarga SOAP al SAT requiere el request firmado con la librería phpcfdi en el servidor.');
    }
}
