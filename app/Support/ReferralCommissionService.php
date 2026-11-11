<?php

namespace App\Support;

use App\Models\Investment;
use App\Models\ReferralEarning;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReferralCommissionService
{
    public const RATE_PERCENTAGE = 5;

    public function creditForInvestment(Investment $investment): void
    {
        DB::transaction(function () use ($investment): void {
            $investment = Investment::query()
                ->whereKey($investment->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($investment->status !== 'approved' || $investment->cancelled_at) {
                return;
            }

            $referredUser = User::query()->find($investment->user_id);
            $referrerId = $referredUser?->referred_by;

            if (! $referrerId || (int) $referrerId === $referredUser?->id) {
                return;
            }

            if (ReferralEarning::query()->where('investment_id', $investment->id)->exists()) {
                return;
            }

            $commission = round((float) $investment->amount * self::RATE_PERCENTAGE / 100, 2);
            if ($commission <= 0) {
                return;
            }

            $referrer = User::query()->whereKey($referrerId)->lockForUpdate()->first();
            if (! $referrer) {
                return;
            }

            ReferralEarning::create([
                'user_id' => $referrer->id,
                'referred_user_id' => $referredUser->id,
                'investment_id' => $investment->id,
                'amount' => $commission,
            ]);

            $referrer->balance = (float) ($referrer->balance ?? 0) + $commission;
            $referrer->save();
        });
    }
}
