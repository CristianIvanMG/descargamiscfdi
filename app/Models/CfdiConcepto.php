<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CfdiConcepto extends Model
{
    protected $fillable = [
        'cfdi_id',
        'clave_prod_serv',
        'descripcion',
        'cantidad',
        'valor_unitario',
        'importe',
        'descuento',
    ];

    protected function casts(): array
    {
        return [
            'cantidad' => 'decimal:6',
            'valor_unitario' => 'decimal:6',
            'importe' => 'decimal:2',
            'descuento' => 'decimal:2',
        ];
    }

    public function cfdi(): BelongsTo
    {
        return $this->belongsTo(Cfdi::class);
    }
}
