<?php

namespace App\Support;

use App\Models\Investment;
use App\Models\User;

class DailyInterestAccrualService
{
    public static function accrueDueInterest(): float
    {
        $totalCredited = 0.0;
        $creditedByUser = [];

        $investments = Investment::query()
            ->where('status', 'approved')
            ->whereNull('cancelled_at')
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
            $credited = $investment->accrueDailyInterest();
            $totalCredited += $credited;

            if ($credited > 0) {
                $creditedByUser[$investment->user_id] = ($creditedByUser[$investment->user_id] ?? 0) + $credited;
            }
        }

        foreach ($creditedByUser as $userId => $amount) {
            $user = User::query()->find($userId);
            if ($user) {
                app(PushNotificationService::class)->sendDailyInterestCredit($user, (float) $amount);
            }
        }

        return round($totalCredited, 2);
    }

    public static function accrueDueInterestForUser(User $user): float
    {
        $totalCredited = 0.0;

        $investments = $user->investments()
            ->where('status', 'approved')
            ->whereNull('cancelled_at')
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

        if ($totalCredited > 0) {
            app(PushNotificationService::class)->sendDailyInterestCredit($user, $totalCredited);
        }

        return round($totalCredited, 2);
    }
}
