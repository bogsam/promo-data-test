<?php

declare(strict_types=1);

return [

    'default' => env(key: 'MAIL_MAILER', default: 'log'),

    'mailers' => [

        'smtp' => [
            'transport'    => 'smtp',
            'scheme'       => env(key: 'MAIL_SCHEME'),
            'url'          => env(key: 'MAIL_URL'),
            'host'         => env(key: 'MAIL_HOST', default: '127.0.0.1'),
            'port'         => env(key: 'MAIL_PORT', default: 2525),
            'username'     => env(key: 'MAIL_USERNAME'),
            'password'     => env(key: 'MAIL_PASSWORD'),
            'timeout'      => null,
            'local_domain' => env(key: 'MAIL_EHLO_DOMAIN', default: parse_url(url: (string) env(key: 'APP_URL', default: 'http://localhost'), component: PHP_URL_HOST)),
        ],

        'ses' => [
            'transport' => 'ses',
        ],

        'postmark' => [
            'transport' => 'postmark',
            // 'message_stream_id' => env('POSTMARK_MESSAGE_STREAM_ID'),
            // 'client' => [
            //     'timeout' => 5,
            // ],
        ],

        'resend' => [
            'transport' => 'resend',
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path'      => env(key: 'MAIL_SENDMAIL_PATH', default: '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel'   => env(key: 'MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers'   => [
                'smtp',
                'log',
            ],
            'retry_after' => 60,
        ],

        'roundrobin' => [
            'transport' => 'roundrobin',
            'mailers'   => [
                'ses',
                'postmark',
            ],
            'retry_after' => 60,
        ],

    ],

    'from' => [
        'address' => env(key: 'MAIL_FROM_ADDRESS', default: 'hello@example.com'),
        'name'    => env(key: 'MAIL_FROM_NAME', default: env(key: 'APP_NAME', default: 'Laravel')),
    ],

];
