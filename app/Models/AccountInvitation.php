<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountInvitation extends Model
{
    protected $fillable = [
        'name',
        'email',
        'token_hash',
        'expires_at',
        'accepted_at',
        'invited_by',
        'referrer_id',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }
}
