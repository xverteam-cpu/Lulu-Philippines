<?php

return [
    'cache_ttl' => env('MARKET_DATA_CACHE_TTL', 60),
    'stale_ttl' => env('MARKET_DATA_STALE_TTL', 86400),
    'eodhd' => [
        'token' => env('EODHD_API_TOKEN'),
        'symbol' => env('EODHD_LULU_SYMBOL', 'LULU.ADX'),
    ],
    'fallback' => [
        'price' => '0.95',
        'change' => '0.00 / -0.11%',
        'volume' => '4,587,021',
        'timestamp' => '04/09/2026 14:55',
    ],
];
