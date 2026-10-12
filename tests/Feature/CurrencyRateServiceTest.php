<?php

namespace Tests\Feature;

use App\Support\CurrencyRateService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CurrencyRateServiceTest extends TestCase
{
    public function test_display_rate_returns_stale_cached_value_without_waiting_for_refresh(): void
    {
        $viewCacheKey = 'usd_to_php_rate_view_meta_v1';
        Cache::forget($viewCacheKey);
        Cache::forget('illuminate:cache:flexible:created:'.$viewCacheKey);
        config([
            'currency.cache_ttl' => 1,
            'currency.stale_ttl' => 60,
        ]);
        Http::fake([
            'api.frankfurter.dev/v1/latest*' => Http::response([
                'date' => '2026-10-12',
                'rates' => ['PHP' => 58.25],
            ]),
        ]);

        try {
            $freshRate = CurrencyRateService::latestUsdToPhpWithMeta();
            $this->assertSame(58.25, $freshRate['rate']);

            \Illuminate\Support\Carbon::setTestNow(now()->addSeconds(2));

            $staleRate = CurrencyRateService::latestUsdToPhpWithMeta();

            $this->assertSame($freshRate, $staleRate);
            Http::assertSentCount(1);
            Http::assertSent(fn (Request $request): bool => str_starts_with(
                $request->url(),
                'https://api.frankfurter.dev/v1/latest'
            ));
        } finally {
            \Illuminate\Support\Carbon::setTestNow();
            Cache::forget($viewCacheKey);
            Cache::forget('illuminate:cache:flexible:created:'.$viewCacheKey);
        }
    }
}
