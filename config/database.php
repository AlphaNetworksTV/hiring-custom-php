<?php

declare(strict_types=1);

return [
    'redis' => [
        'client' => 'phpredis',
        'default' => [
            'url' => env('REDIS_CACHE_URL'),
        ],
    ],
];
