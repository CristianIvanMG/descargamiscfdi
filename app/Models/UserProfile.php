<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'business_name',
        'rfc',
        'user_type',
        'primary_email',
        'country',
        'estado',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isComplete(): bool
    {
        $validTypes = ['Contador independiente', 'Persona fisica', 'Persona física', 'Persona fÃ­sica', 'Despacho contable'];

        $state = $this->estado ?? $this->country;

        return $this->business_name !== null
            && in_array($this->user_type, $validTypes, true)
            && $this->primary_email !== null
            && $this->rfc !== null
            && $state !== null
            && ! in_array($state, ['Mexico', 'México', 'MÃ©xico'], true)
            && $this->completed_at !== null;
    }
}
