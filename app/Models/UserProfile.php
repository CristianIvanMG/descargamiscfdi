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
        return $this->business_name !== null
            && $this->user_type !== null
            && $this->primary_email !== null
            && $this->country !== null
            && $this->completed_at !== null;
    }
}
