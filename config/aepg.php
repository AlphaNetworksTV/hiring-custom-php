<?php

declare(strict_types=1);


return [
    'version' => env('APP_VERSION', 'dev'),
    'metrics_push_gateway' => env('METRICS_PUSH_GATEWAY'),
    'epg_storage_url' => env('STORAGE_ENDPOINT'),
    'epg_storage_key' => env('STORAGE_ACCESS_KEY'),
    'epg_storage_password' => env('STORAGE_SECRET_KEY'),
    'epg_storage_bucket' => env('STORAGE_BUCKET'),
    'epg_storage_region' => env('STORAGE_REGION'),
    'epg_storage_folder' => env('STORAGE_FOLDER'),
];
