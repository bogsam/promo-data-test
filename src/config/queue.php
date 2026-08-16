<?php

declare(strict_types=1);

return [

    'default' => env(key: 'QUEUE_CONNECTION', default: 'redis'),

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
            'driver'       => 'database',
            'connection'   => env(key: 'DB_QUEUE_CONNECTION'),
            'table'        => env(key: 'DB_QUEUE_TABLE', default: 'jobs'),
            'queue'        => env(key: 'DB_QUEUE', default: 'default'),
            'retry_after'  => (int) env(key: 'DB_QUEUE_RETRY_AFTER', default: 90),
            'after_commit' => false,
        ],

        'beanstalkd' => [
            'driver'       => 'beanstalkd',
            'host'         => env(key: 'BEANSTALKD_QUEUE_HOST', default: 'localhost'),
            'queue'        => env(key: 'BEANSTALKD_QUEUE', default: 'default'),
            'retry_after'  => (int) env(key: 'BEANSTALKD_QUEUE_RETRY_AFTER', default: 90),
            'block_for'    => 0,
            'after_commit' => false,
        ],

        'sqs' => [
            'driver'       => 'sqs',
            'key'          => env(key: 'AWS_ACCESS_KEY_ID'),
            'secret'       => env(key: 'AWS_SECRET_ACCESS_KEY'),
            'prefix'       => env(key: 'SQS_PREFIX', default: 'https://sqs.us-east-1.amazonaws.com/your-account-id'),
            'queue'        => env(key: 'SQS_QUEUE', default: 'default'),
            'suffix'       => env(key: 'SQS_SUFFIX'),
            'region'       => env(key: 'AWS_DEFAULT_REGION', default: 'us-east-1'),
            'after_commit' => false,
        ],

        'redis' => [
            'driver'       => 'redis',
            'connection'   => env(key: 'REDIS_QUEUE_CONNECTION', default: 'default'),
            'queue'        => env(key: 'REDIS_QUEUE', default: 'default'),
            'retry_after'  => (int) env(key: 'REDIS_QUEUE_RETRY_AFTER', default: 90),
            'block_for'    => null,
            'after_commit' => false,
        ],

        'deferred' => [
            'driver' => 'deferred',
        ],

        'background' => [
            'driver' => 'background',
        ],

        'failover' => [
            'driver'      => 'failover',
            'connections' => [
                'redis',
                'deferred',
            ],
        ],

    ],

    'batching' => [
        'database' => env(key: 'DB_CONNECTION', default: 'pgsql'),
        'table'    => 'job_batches',
    ],

    'failed' => [
        'driver'   => env(key: 'QUEUE_FAILED_DRIVER', default: 'database-uuids'),
        'database' => env(key: 'DB_CONNECTION', default: 'pgsql'),
        'table'    => 'failed_jobs',
    ],

];
