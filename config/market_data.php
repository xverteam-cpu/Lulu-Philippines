<?php

return [
    'cache_ttl' => env('MARKET_DATA_CACHE_TTL', 60),
    'stale_ttl' => env('MARKET_DATA_STALE_TTL', 86400),
    'eodhd' => [
        'token' => env('EODHD_API_TOKEN'),
        'symbol' => env('EODHD_LULU_SYMBOL', 'LULU.ADX'),
    ],
];
