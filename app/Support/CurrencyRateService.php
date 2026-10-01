<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CurrencyRateService
{
    public static function latestUsdToPhp(): float
    {
        return (float) self::latestUsdToPhpWithMeta()['rate'];
    }

    private static function metaCacheKey(): string
    {
        return 'usd_to_php_rate_meta_v2';
    }

    public static function latestUsdToPhpWithMeta(): array
    {
        return Cache::remember(
            self::metaCacheKey(),
            config('currency.cache_ttl', 3600),
            fn (): array => self::fetchFromApi()
        );
    }

    /**
     * @return array{rate: float, updated_at: string}
     */
    private static function fetchFromApi(): array
    {
        $response = Http::acceptJson()
            ->timeout(5)
            ->get('https://api.frankfurter.dev/v1/latest', [
                'base' => 'USD',
                'symbols' => 'PHP',
            ]);

        $response->throw();
        $rate = $response->json('rates.PHP');

        if (! is_numeric($rate) || (float) $rate <= 0) {
            throw new \RuntimeException('The exchange-rate provider returned an invalid USD to PHP rate.');
        }

        $updatedAt = $response->json('date');

        return [
            'rate' => round((float) $rate, 4),
            'updated_at' => is_string($updatedAt) ? $updatedAt : now()->toDateString(),
        ];
    }
}
