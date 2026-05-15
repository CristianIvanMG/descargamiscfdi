<?php

namespace App\Models;

use App\Enums\DescargaEstado;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DescargaJob extends Model
{
    protected $fillable = [
        'user_id',
        'rfc_id',
        'estado',
        'tipo',
        'fecha_inicio',
        'fecha_fin',
        'solicitud_id',
        'paquete_id',
        'total_cfdi',
        'mensaje_error',
        'iniciado_en',
        'terminado_en',
    ];

    protected function casts(): array
    {
        return [
            'estado' => DescargaEstado::class,
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'total_cfdi' => 'integer',
            'iniciado_en' => 'datetime',
            'terminado_en' => 'datetime',
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
}
