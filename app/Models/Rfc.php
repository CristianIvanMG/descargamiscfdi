<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rfc extends Model
{
    protected $fillable = [
        'user_id',
        'rfc',
        'razon_social',
        'regimen_fiscal',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cfdis(): HasMany
    {
        return $this->hasMany(Cfdi::class);
    }

    public function descargas(): HasMany
    {
        return $this->hasMany(DescargaJob::class);
    }
}
