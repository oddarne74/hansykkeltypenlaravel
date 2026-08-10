<?php

return [

    'name' => env('APP_NAME', 'Han Sykkeltypen'),

    'env' => env('APP_ENV', 'production'),

    'debug' => (bool) env('APP_DEBUG', false),

    'url' => env('APP_URL', 'https://hansykkeltypen.cc'),

    'timezone' => 'UTC',

    'locale' => env('APP_LOCALE', 'nb'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'nb'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'nb_NO'),

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => array_filter(
        explode(',', env('APP_PREVIOUS_KEYS', ''))
    ),

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

];
