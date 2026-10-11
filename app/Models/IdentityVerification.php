<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IdentityVerification extends Model
{
    public const ID_TYPES = [
        'drivers_license' => "Driver's license",
        'national_id' => 'Philippine National ID',
        'sss' => 'SSS ID',
        'umid' => 'UMID',
        'passport' => 'Philippine passport',
        'philhealth' => 'PhilHealth ID',
        'pagibig' => 'Pag-IBIG ID',
        'voters' => "Voter's ID",
    ];

    protected $fillable = [
        'user_id',
        'id_type',
        'front_image_path',
        'back_image_path',
        'selfie_image_path',
        'status',
        'review_note',
        'reviewed_by',
        'submitted_at',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
