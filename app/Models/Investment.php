<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\ReferralEarning;

class Investment extends Model
{
    protected $fillable = [
        'user_id',
        'package_key',
        'package_name',
        'package_price',
        'amount',
        'payment_method',
        'daily_interest_rate',
        'duration_days',
        'starts_at',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'interest_days_credited',
        'last_interest_accrued_at',
        'agreement_version',
        'agreement_signature_name',
        'agreement_signed_at',
    ];

    protected function casts(): array
    {
        return [
            'package_price' => 'decimal:2',
            'amount' => 'decimal:2',
            'daily_interest_rate' => 'decimal:3',
            'duration_days' => 'integer',
            'interest_days_credited' => 'integer',
            'starts_at' => 'datetime',
            'approved_at' => 'datetime',
            'last_interest_accrued_at' => 'datetime',
            'agreement_signed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function dailyInterestAmount(): float
    {
        return (float) $this->amount * ((float) $this->daily_interest_rate / 100);
    }

    public function elapsedInterestDays(): int
    {
        if (! $this->starts_at) {
            return 0;
        }

        return min(
            $this->duration_days,
            max(0, (int) floor($this->starts_at->diffInDays(now())))
        );
    }

    public function earnedInterest(): float
    {
        return $this->dailyInterestAmount() * $this->elapsedInterestDays();
    }

    public function creditedInterest(): float
    {
        return $this->dailyInterestAmount() * (int) ($this->interest_days_credited ?? 0);
    }

    public function accrueDailyInterest(): float
    {
        if ($this->status !== 'approved' || ! $this->starts_at) {
            return 0.0;
        }

        $creditedDays = (int) $this->interest_days_credited;
        if ($creditedDays >= $this->duration_days) {
            return 0.0;
        }

        $today = now()->startOfDay();
        $startDate = $this->starts_at->startOfDay();
        $nextDueDate = $this->last_interest_accrued_at
            ? $this->last_interest_accrued_at->copy()->startOfDay()->addDay()
            : $startDate->copy()->addDay();

        if ($today->lt($nextDueDate)) {
            return 0.0;
        }

        $daysDue = $today->diffInDays($nextDueDate) + 1;
        $daysDue = min($daysDue, $this->duration_days - $creditedDays);

        if ($daysDue <= 0) {
            return 0.0;
        }

        $interest = round($this->dailyInterestAmount() * $daysDue, 2);
        $this->interest_days_credited = $creditedDays + $daysDue;
        $this->last_interest_accrued_at = $today;
        $this->save();

        $user = $this->user;
        if ($user) {
            $user->balance = ($user->balance ?? 0) + $interest;
            $user->save();
        }

        return $interest;
    }

    /**
     * If the investing user was referred, credit the referrer 5% commission of the capital.
     */
    public function processReferralCommission(): void
    {
        $user = $this->user;
        if (! $user || ! $user->referred_by) {
            return;
        }

        $referrer = User::find($user->referred_by);
        if (! $referrer) {
            return;
        }

        $commission = round((float) $this->amount * 0.05, 2);
        if ($commission <= 0) {
            return;
        }

        // Credit referrer balance
        $referrer->balance = ($referrer->balance ?? 0) + $commission;
        $referrer->save();

        // Record the referral earning
        ReferralEarning::create([
            'user_id' => $referrer->id,
            'referred_user_id' => $user->id,
            'investment_id' => $this->id,
            'amount' => $commission,
        ]);
    }
}
