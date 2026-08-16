<?php

declare(strict_types=1);

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Processor\PsrLogMessageProcessor;

return [

    'default' => env(key: 'LOG_CHANNEL', default: 'stack'),

    'deprecations' => [
        'channel' => env(key: 'LOG_DEPRECATIONS_CHANNEL', default: 'null'),
        'trace'   => env(key: 'LOG_DEPRECATIONS_TRACE', default: false),
    ],

    'channels' => [

        'stack' => [
            'driver'            => 'stack',
            'channels'          => explode(separator: ',', string: (string) env(key: 'LOG_STACK', default: 'single')),
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver'               => 'single',
            'path'                 => storage_path(path: 'logs/laravel.log'),
            'level'                => env(key: 'LOG_LEVEL', default: 'debug'),
            'replace_placeholders' => true,
        ],

        'daily' => [
            'driver'               => 'daily',
            'path'                 => storage_path(path: 'logs/laravel.log'),
            'level'                => env(key: 'LOG_LEVEL', default: 'debug'),
            'days'                 => env(key: 'LOG_DAILY_DAYS', default: 14),
            'replace_placeholders' => true,
        ],

        'slack' => [
            'driver'               => 'slack',
            'url'                  => env(key: 'LOG_SLACK_WEBHOOK_URL'),
            'username'             => env(key: 'LOG_SLACK_USERNAME', default: env(key: 'APP_NAME', default: 'Laravel')),
            'emoji'                => env(key: 'LOG_SLACK_EMOJI', default: ':boom:'),
            'level'                => env(key: 'LOG_LEVEL', default: 'critical'),
            'replace_placeholders' => true,
        ],

        'papertrail' => [
            'driver'       => 'monolog',
            'level'        => env(key: 'LOG_LEVEL', default: 'debug'),
            'handler'      => env(key: 'LOG_PAPERTRAIL_HANDLER', default: SyslogUdpHandler::class),
            'handler_with' => [
                'host'             => env(key: 'PAPERTRAIL_URL'),
                'port'             => env(key: 'PAPERTRAIL_PORT'),
                'connectionString' => 'tls://' . env(key: 'PAPERTRAIL_URL') . ':' . env(key: 'PAPERTRAIL_PORT'),
            ],
            'processors' => [PsrLogMessageProcessor::class],
        ],

        'stderr' => [
            'driver'       => 'monolog',
            'level'        => env(key: 'LOG_LEVEL', default: 'debug'),
            'handler'      => StreamHandler::class,
            'handler_with' => [
                'stream' => 'php://stderr',
            ],
            'formatter'  => env(key: 'LOG_STDERR_FORMATTER'),
            'processors' => [PsrLogMessageProcessor::class],
        ],

        'syslog' => [
            'driver'               => 'syslog',
            'level'                => env(key: 'LOG_LEVEL', default: 'debug'),
            'facility'             => env(key: 'LOG_SYSLOG_FACILITY', default: LOG_USER),
            'replace_placeholders' => true,
        ],

        'errorlog' => [
            'driver'               => 'errorlog',
            'level'                => env(key: 'LOG_LEVEL', default: 'debug'),
            'replace_placeholders' => true,
        ],

        'null' => [
            'driver'  => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path(path: 'logs/laravel.log'),
        ],

    ],

];
