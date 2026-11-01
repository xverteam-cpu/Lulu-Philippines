<?php

namespace App\Support;

use App\Models\User;
use InvalidArgumentException;

class CurrencyDisplay
{
    public static function preferredFor(User $user): string
    {
        return ! $user->is_admin && $user->preferred_currency === 'PHP' ? 'PHP' : 'USD';
    }

    public static function fromUsd(float|int|string $amount, string $currency, float $usdToPhpRate): float
    {
        return match ($currency) {
            'USD' => (float) $amount,
            'PHP' => (float) $amount * $usdToPhpRate,
            default => throw new InvalidArgumentException('Unsupported display currency.'),
        };
    }

    public static function formatUsd(float|int|string $amount, string $currency, float $usdToPhpRate): string
    {
        $convertedAmount = self::fromUsd($amount, $currency, $usdToPhpRate);
        $symbol = match ($currency) {
            'USD' => '$',
            'PHP' => '₱',
            default => throw new InvalidArgumentException('Unsupported display currency.'),
        };

        return $symbol.number_format($convertedAmount, 2);
    }

    public static function toUsd(float|int|string $amount, string $currency, float $usdToPhpRate): float
    {
        return match ($currency) {
            'USD' => round((float) $amount, 2),
            'PHP' => $usdToPhpRate > 0
                ? round((float) $amount / $usdToPhpRate, 2)
                : throw new InvalidArgumentException('The USD to PHP rate must be positive.'),
            default => throw new InvalidArgumentException('Unsupported input currency.'),
        };
    }
}
