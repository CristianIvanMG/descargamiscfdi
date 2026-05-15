<?php

namespace App\Services;

use RuntimeException;

class PacService
{
    public function timbrarCobro(array $payload): string
    {
        throw new RuntimeException('Timbrado PAC se implementa despues de pagos y facturacion.');
    }
}
