<?php

declare(strict_types=1);

use Illuminate\Support\Str;

return [

    'default' => env(key: 'DB_CONNECTION', default: 'pgsql'),

    'connections' => [

        'sqlite' => [
            'driver'                  => 'sqlite',
            'url'                     => env(key: 'DB_URL'),
            'database'                => env(key: 'DB_DATABASE', default: ':memory:'),
            'prefix'                  => '',
            'foreign_key_constraints' => env(key: 'DB_FOREIGN_KEYS', default: true),
            'busy_timeout'            => null,
            'journal_mode'            => null,
            'synchronous'             => null,
            'transaction_mode'        => 'DEFERRED',
        ],

        'pgsql' => [
            'driver'         => 'pgsql',
            'url'            => env(key: 'DB_URL'),
            'host'           => env(key: 'DB_HOST', default: 'db'),
            'port'           => env(key: 'DB_PORT', default: '5432'),
            'database'       => env(key: 'DB_DATABASE', default: 'app'),
            'username'       => env(key: 'DB_USERNAME', default: 'app'),
            'password'       => env(key: 'DB_PASSWORD', default: 'password'),
            'charset'        => env(key: 'DB_CHARSET', default: 'utf8'),
            'prefix'         => '',
            'prefix_indexes' => true,
            'search_path'    => 'public',
            'sslmode'        => env(key: 'DB_SSLMODE', default: 'prefer'),
        ],
    ],

    'migrations' => [
        'table'                  => 'migrations',
        'update_date_on_publish' => true,
    ],

    'redis' => [

        'client' => env(key: 'REDIS_CLIENT', default: 'phpredis'),

        'options' => [
            'cluster'    => env(key: 'REDIS_CLUSTER', default: 'redis'),
            'prefix'     => env(key: 'REDIS_PREFIX', default: Str::slug(title: (string) env(key: 'APP_NAME', default: 'laravel')) . '-database-'),
            'persistent' => env(key: 'REDIS_PERSISTENT', default: false),
        ],

        'default' => [
            'url'               => env(key: 'REDIS_URL'),
            'host'              => env(key: 'REDIS_HOST', default: '127.0.0.1'),
            'username'          => env(key: 'REDIS_USERNAME'),
            'password'          => env(key: 'REDIS_PASSWORD'),
            'port'              => env(key: 'REDIS_PORT', default: '6379'),
            'database'          => env(key: 'REDIS_DB', default: '0'),
            'max_retries'       => env(key: 'REDIS_MAX_RETRIES', default: 3),
            'backoff_algorithm' => env(key: 'REDIS_BACKOFF_ALGORITHM', default: 'decorrelated_jitter'),
            'backoff_base'      => env(key: 'REDIS_BACKOFF_BASE', default: 100),
            'backoff_cap'       => env(key: 'REDIS_BACKOFF_CAP', default: 1000),
        ],

        'cache' => [
            'url'               => env(key: 'REDIS_URL'),
            'host'              => env(key: 'REDIS_HOST', default: '127.0.0.1'),
            'username'          => env(key: 'REDIS_USERNAME'),
            'password'          => env(key: 'REDIS_PASSWORD'),
            'port'              => env(key: 'REDIS_PORT', default: '6379'),
            'database'          => env(key: 'REDIS_CACHE_DB', default: '1'),
            'max_retries'       => env(key: 'REDIS_MAX_RETRIES', default: 3),
            'backoff_algorithm' => env(key: 'REDIS_BACKOFF_ALGORITHM', default: 'decorrelated_jitter'),
            'backoff_base'      => env(key: 'REDIS_BACKOFF_BASE', default: 100),
            'backoff_cap'       => env(key: 'REDIS_BACKOFF_CAP', default: 1000),
        ],

    ],

];
