<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'address',
        'region',
        'message',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'withdrawal_account_type',
        'password',
        'referred_by',
        'signup_bonus_claimed_at',
        'is_admin',
        'is_restricted',
        'restricted_ip_address',
        'last_seen_at',
        'last_ip_address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_restricted' => 'boolean',
            'last_seen_at' => 'datetime',
            'notifications_read' => 'array',
        ];
    }

    public function getNotificationsReadIds(): array
    {
        return is_array($this->notifications_read) ? $this->notifications_read : [];
    }

    public function markNotificationsRead(array $ids): void
    {
        $updated = collect($this->getNotificationsReadIds())
            ->merge($ids)
            ->unique()
            ->values()
            ->all();

        $this->notifications_read = $updated;
        $this->save();
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function isOnline(): bool
    {
        return $this->last_seen_at
            && $this->last_seen_at->greaterThan(now()->subMinutes(5));
    }

    public function investments(): HasMany
    {
        return $this->hasMany(Investment::class);
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class, 'user_id');
    }

    public function referralEarnings(): HasMany
    {
        return $this->hasMany(\App\Models\ReferralEarning::class, 'user_id');
    }

    public function shouldShowRafflePopup(): bool
    {
        if (! $this->raffle_popup_last_shown_at) {
            return true;
        }

        $lastShownAt = $this->raffle_popup_last_shown_at instanceof Carbon
            ? $this->raffle_popup_last_shown_at
            : Carbon::parse($this->raffle_popup_last_shown_at);

        return now()->startOfDay()->gt($lastShownAt->startOfDay());
    }

    public function markRafflePopupShown(): void
    {
        $this->raffle_popup_shown_count = ($this->raffle_popup_shown_count ?? 0) + 1;
        $this->raffle_popup_last_shown_at = now();
        $this->save();
    }
}
