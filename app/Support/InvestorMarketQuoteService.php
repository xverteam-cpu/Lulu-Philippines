<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class InvestorMarketQuoteService
{
    public static function latest(): array
    {
        $freshTtl = max(1, (int) config('market_data.cache_ttl', 60));
        $staleTtl = max($freshTtl, (int) config('market_data.stale_ttl', 86400));

        return Cache::flexible(
            'investors.lulu_retail_quote.v1',
            [$freshTtl, $staleTtl],
            fn (): array => self::fetch()
        );
    }

    private static function fetch(): array
    {
        $token = (string) config('market_data.eodhd.token', '');
        $symbol = (string) config('market_data.eodhd.symbol', 'LULU.ADX');

        if ($token === '') {
            throw new RuntimeException('EODHD market data token is not configured.');
        }

        $response = Http::acceptJson()
            ->timeout(5)
            ->get('https://eodhd.com/api/real-time/'.$symbol, [
                'api_token' => $token,
                'fmt' => 'json',
            ]);

        $response->throw();

        $data = $response->json();
        if (
            ! is_array($data)
            || ! is_numeric($data['close'] ?? null)
            || ! is_numeric($data['change'] ?? null)
            || ! is_numeric($data['change_p'] ?? null)
            || ! is_numeric($data['volume'] ?? null)
            || ! is_numeric($data['timestamp'] ?? null)
        ) {
            throw new RuntimeException('EODHD returned an incomplete Lulu Retail quote.');
        }

        return [
            'price' => (float) $data['close'],
            'change' => (float) $data['change'],
            'change_percent' => (float) $data['change_p'],
            'volume' => (int) $data['volume'],
            'timestamp' => (int) $data['timestamp'],
        ];
    }
}
