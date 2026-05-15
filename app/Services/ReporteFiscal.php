<?php

namespace App\Services;

use Illuminate\Support\Collection;

class ReporteFiscal
{
    public function resumen(Collection $cfdis): array
    {
        return [
            'subtotal' => $cfdis->sum('subtotal'),
            'iva' => $cfdis->sum('iva'),
            'total' => $cfdis->sum('total'),
        ];
    }
}
