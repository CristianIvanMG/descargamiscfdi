<?php

namespace App\Models;

use App\Enums\CfdiEstatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cfdi extends Model
{
    protected $fillable = [
        'user_id',
        'rfc_id',
        'uuid',
        'tipo',
        'serie',
        'folio',
        'rfc_emisor',
        'nombre_emisor',
        'rfc_receptor',
        'nombre_receptor',
        'fecha_emision',
        'subtotal',
        'descuento',
        'iva',
        'total',
        'moneda',
        'estatus',
        'xml_path',
    ];

    protected function casts(): array
    {
        return [
            'fecha_emision' => 'datetime',
            'subtotal' => 'decimal:2',
            'descuento' => 'decimal:2',
            'iva' => 'decimal:2',
            'total' => 'decimal:2',
            'estatus' => CfdiEstatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rfc(): BelongsTo
    {
        return $this->belongsTo(Rfc::class);
    }

    public function conceptos(): HasMany
    {
        return $this->hasMany(CfdiConcepto::class);
    }
}
