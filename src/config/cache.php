<?php

declare(strict_types=1);

use Illuminate\Support\Str;

return [

    'default' => env(key: 'CACHE_STORE', default: 'redis'),

    'stores' => [

        'array' => [
            'driver'    => 'array',
            'serialize' => false,
        ],

        'database' => [
            'driver'          => 'database',
            'connection'      => env(key: 'DB_CACHE_CONNECTION'),
            'table'           => env(key: 'DB_CACHE_TABLE', default: 'cache'),
            'lock_connection' => env(key: 'DB_CACHE_LOCK_CONNECTION'),
            'lock_table'      => env(key: 'DB_CACHE_LOCK_TABLE'),
        ],

        'file' => [
            'driver'    => 'file',
            'path'      => storage_path(path: 'framework/cache/data'),
            'lock_path' => storage_path(path: 'framework/cache/data'),
        ],

        'storage' => [
            'driver' => 'storage',
            'disk'   => env(key: 'CACHE_STORAGE_DISK'),
            'path'   => env(key: 'CACHE_STORAGE_PATH', default: 'framework/cache/data'),
        ],

        'redis' => [
            'driver'          => 'redis',
            'connection'      => env(key: 'REDIS_CACHE_CONNECTION', default: 'cache'),
            'lock_connection' => env(key: 'REDIS_CACHE_LOCK_CONNECTION', default: 'default'),
        ],

        'octane' => [
            'driver' => 'octane',
        ],

        'failover' => [
            'driver' => 'failover',
            'stores' => [
                'redis',
                'array',
            ],
        ],

    ],

    'prefix' => env(key: 'CACHE_PREFIX', default: Str::slug(title: (string) env(key: 'APP_NAME', default: 'laravel')) . '-cache-'),

    'serializable_classes' => false,

];
