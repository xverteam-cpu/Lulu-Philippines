<?php

namespace App\Support;

use App\Models\Investment;
use App\Models\User;
use Illuminate\Support\Carbon;

class DailyInterestAccrualService
{
    public static function accrueDueInterest(): float
    {
        $totalCredited = 0.0;

        $investments = Investment::query()
            ->where('status', 'approved')
            ->where(function ($query) {
                $query->whereNotNull('starts_at')
                    ->orWhereNotNull('approved_at');
            })
            ->where(function ($query) {
                $query->where('starts_at', '<=', now())
                    ->orWhere('approved_at', '<=', now());
            })
            ->get();

        foreach ($investments as $investment) {
            $totalCredited += $investment->accrueDailyInterest();
        }

        return round($totalCredited, 2);
    }

    public static function accrueDueInterestForUser(User $user): float
    {
        $totalCredited = 0.0;

        $investments = $user->investments()
            ->where('status', 'approved')
            ->where(function ($query) {
                $query->whereNotNull('starts_at')
                    ->orWhereNotNull('approved_at');
            })
            ->where(function ($query) {
                $query->where('starts_at', '<=', now())
                    ->orWhere('approved_at', '<=', now());
            })
            ->get();

        foreach ($investments as $investment) {
            $totalCredited += $investment->accrueDailyInterest();
        }

        return round($totalCredited, 2);
    }
}
