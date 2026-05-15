<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Suscripcion extends Model
{
    protected $fillable = [
        'user_id',
        'plan',
        'proveedor_pago',
        'proveedor_id',
        'estatus',
        'periodo_inicio',
        'periodo_fin',
        'renovacion_automatica',
    ];

    protected function casts(): array
    {
        return [
            'periodo_inicio' => 'datetime',
            'periodo_fin' => 'datetime',
            'renovacion_automatica' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
