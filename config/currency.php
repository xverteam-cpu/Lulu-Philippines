<?php

return [
    'cache_ttl' => env('CURRENCY_RATE_CACHE_TTL', 3600),
    'stale_ttl' => env('CURRENCY_RATE_STALE_TTL', 86400),
];
