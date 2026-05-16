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
        $validTypes = ['Contador independiente', 'Persona física', 'Despacho contable'];

        return $this->business_name !== null
            && in_array($this->user_type, $validTypes, true)
            && $this->primary_email !== null
            && $this->rfc !== null
            && $this->country !== null
            && $this->country !== 'México'
            && $this->completed_at !== null;
    }
}
