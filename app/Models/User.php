<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

class User extends Authenticatable
{
    public const ONLINE_WINDOW_MINUTES = 5;

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
        'referral_code',
        'email',
        'phone',
        'phone_verified_at',
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
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_restricted' => 'boolean',
            'last_seen_at' => 'datetime',
            'notifications_read' => 'array',
            'notification_preferences' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (User $user): void {
            if (! $user->referral_code) {
                $user->referral_code = static::makeUniqueReferralCode((string) $user->name);
            }
        });
    }

    public static function findByReferralIdentifier(string $identifier): ?self
    {
        $identifier = trim($identifier);

        return static::query()
            ->whereRaw('LOWER(referral_code) = ?', [mb_strtolower($identifier)])
            ->first()
            ?? static::query()
                ->whereRaw('LOWER(username) = ?', [mb_strtolower($identifier)])
                ->first();
    }

    public static function makeUniqueReferralCode(string $name): string
    {
        $parts = preg_split('/\s+/', trim(preg_replace('/[^a-zA-Z0-9]+/', ' ', \Illuminate\Support\Str::ascii($name)) ?: ''));
        $firstName = $parts[0] ?? 'User';
        $surnameInitial = count($parts) > 1 ? strtoupper(substr((string) end($parts), 0, 1)) : '';
        $base = ucfirst(strtolower($firstName)).$surnameInitial;
        $base = substr($base, 0, 240) ?: 'User';
        $candidate = $base;
        $suffix = 2;

        while (static::query()->whereRaw('LOWER(referral_code) = ?', [mb_strtolower($candidate)])->exists()) {
            $candidate = $base.$suffix;
            $suffix++;
        }

        return $candidate;
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
            && $this->last_seen_at->greaterThanOrEqualTo(now()->subMinutes(self::ONLINE_WINDOW_MINUTES));
    }

    public function investments(): HasMany
    {
        return $this->hasMany(Investment::class);
    }

    public function latestSignedInvestment(): HasOne
    {
        return $this->hasOne(Investment::class)
            ->whereNotNull('agreement_signature_data')
            ->latestOfMany('agreement_signed_at');
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

    public function identityVerification(): HasOne
    {
        return $this->hasOne(IdentityVerification::class);
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
