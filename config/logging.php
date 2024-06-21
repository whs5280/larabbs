<?php

use App\Logging\CustomizeFormatter;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => 'critical',
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => 'debug',
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

        'sql' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/sql.log'),
            'level' => 'debug',
            'days'  => 14,
            'replace_placeholders' => true,
        ],

        'json' => [
            'driver' => 'single',
            'tap' => [CustomizeFormatter::class],
            'path' => storage_path('logs/laravel-json.log'),
            'level' => 'debug',
        ],

        'mq' => [
            'driver' => 'single',
            'tap' => [CustomizeFormatter::class],
            'path' => storage_path('logs/mq.log'),
            'level' => 'debug',
        ],
    ],


    'jaeger' => [
        'host'  => env('JAEGER_AGENT_HOST', '127.0.0.1'),
        'port'  => env('JAEGER_AGENT_PORT', '16686'),
        'name'  => env('JAEGER_SERVICE_NAME', 'lara-bbs')
    ],


    'query' => [
        'enabled'     => env('DB_QUERY_LOG_ENABLE', true),
        'slower_than' => env('DB_QUERY_LOG_SLOWER_THAN', 0.1)
    ],
];
