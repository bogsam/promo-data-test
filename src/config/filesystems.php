<?php

declare(strict_types=1);

return [

    'default' => env(key: 'FILESYSTEM_DISK', default: 'local'),

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root'   => storage_path(path: 'app/private'),
            'serve'  => true,
            'throw'  => false,
        ],

        'public' => [
            'driver'     => 'local',
            'root'       => storage_path(path: 'app/public'),
            'url'        => rtrim(string: env(key: 'APP_URL', default: 'http://localhost'), characters: '/') . '/storage',
            'visibility' => 'public',
            'throw'      => false,
        ],

        's3' => [
            'driver'                  => 's3',
            'key'                     => env(key: 'AWS_ACCESS_KEY_ID'),
            'secret'                  => env(key: 'AWS_SECRET_ACCESS_KEY'),
            'region'                  => env(key: 'AWS_DEFAULT_REGION'),
            'bucket'                  => env(key: 'AWS_BUCKET'),
            'url'                     => env(key: 'AWS_URL'),
            'endpoint'                => env(key: 'AWS_ENDPOINT'),
            'use_path_style_endpoint' => env(key: 'AWS_USE_PATH_STYLE_ENDPOINT', default: false),
            'throw'                   => false,
        ],

    ],

    'links' => [
        public_path(path: 'storage') => storage_path(path: 'app/public'),
    ],

];
