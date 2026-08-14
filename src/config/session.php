<?php

declare(strict_types=1);

use Illuminate\Support\Str;

return [

    'driver' => env(key: 'SESSION_DRIVER', default: 'database'),

    'lifetime' => (int) env(key: 'SESSION_LIFETIME', default: 120),

    'expire_on_close' => env(key: 'SESSION_EXPIRE_ON_CLOSE', default: false),

    'encrypt' => env(key: 'SESSION_ENCRYPT', default: false),

    'files' => storage_path(path: 'framework/sessions'),

    'connection' => env(key: 'SESSION_CONNECTION'),

    'table' => env(key: 'SESSION_TABLE', default: 'sessions'),

    'store' => env(key: 'SESSION_STORE'),

    'lottery' => [2, 100],

    'cookie' => env(
        key: 'SESSION_COOKIE',
        default: Str::slug(title: (string) env(key: 'APP_NAME', default: 'laravel')) . '-session'
    ),

    'path' => env(key: 'SESSION_PATH', default: '/'),

    'domain' => env(key: 'SESSION_DOMAIN'),

    'secure' => env(key: 'SESSION_SECURE_COOKIE'),

    'http_only' => env(key: 'SESSION_HTTP_ONLY', default: true),

    'same_site' => env(key: 'SESSION_SAME_SITE', default: 'lax'),

    'partitioned' => env(key: 'SESSION_PARTITIONED_COOKIE', default: false),

    'serialization' => 'json',

];
