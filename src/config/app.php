<?php

declare(strict_types=1);

return [

    'name' => env(key: 'APP_NAME', default: 'Laravel'),

    'env' => env(key: 'APP_ENV', default: 'production'),

    'debug' => (bool) env(key: 'APP_DEBUG', default: false),

    'url' => env(key: 'APP_URL', default: 'http://localhost'),

    'timezone' => 'UTC',

    'locale' => env(key: 'APP_LOCALE', default: 'en'),

    'fallback_locale' => env(key: 'APP_FALLBACK_LOCALE', default: 'en'),

    'faker_locale' => env(key: 'APP_FAKER_LOCALE', default: 'en_US'),

    'cipher' => 'AES-256-CBC',

    'key' => env(key: 'APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            array: explode(separator: ',', string: (string) env(key: 'APP_PREVIOUS_KEYS', default: ''))
        ),
    ],

    'maintenance' => [
        'driver' => env(key: 'APP_MAINTENANCE_DRIVER', default: 'file'),
        'store'  => env(key: 'APP_MAINTENANCE_STORE', default: 'database'),
    ],

];
