<?php

/** @noinspection PhpUnusedAliasInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

use App\Models\HttpLog;
use App\Support\Monolog\Formatter\EloquentLogHttpModelFormatter;
use App\Support\Monolog\Handler\EloquentHandler;
use App\Support\Monolog\Processor\EloquentLogHttpModelProcessor;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Processor\LoadAverageProcessor;
use Monolog\Processor\MemoryPeakUsageProcessor;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\PsrLogMessageProcessor;

return [
    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that is utilized to write
    | messages to your logs. The value provided here should match one of
    | the channels present in the list of "channels" configured below.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Deprecations Log Channel
    |--------------------------------------------------------------------------
    |
    | This option controls the log channel that should be used to log warnings
    | regarding deprecated PHP and library features. This allows you to get
    | your application ready for upcoming major versions of dependencies.
    |
    */

    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace' => env('LOG_DEPRECATIONS_TRACE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Laravel
    | utilizes the Monolog PHP logging library, which includes a variety
    | of powerful log handlers and formatters that you're free to use.
    |
    | Available drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog", "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => explode(',', env('LOG_STACK', 'single')),
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => env('LOG_DAILY_DAYS', 14),
            'replace_placeholders' => true,
        ],

        'daily-query' => [
            'driver' => 'daily',
            'path' => storage_path('logs/query/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => env('LOG_DAILY_DAYS', 14),
            'replace_placeholders' => true,
        ],

        'daily-deprecations' => [
            'driver' => 'daily',
            'path' => storage_path('logs/deprecations/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 3,
            'replace_placeholders' => true,
        ],

        'daily-elasticsearch' => [
            'driver' => 'daily',
            'path' => storage_path('logs/elasticsearch/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 30,
            'replace_placeholders' => true,
        ],

        'daily-http' => [
            'driver' => 'daily',
            'path' => storage_path('logs/http/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 30,
            'replace_placeholders' => true,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => env('LOG_SLACK_USERNAME', 'Laravel Log'),
            'emoji' => env('LOG_SLACK_EMOJI', ':boom:'),
            'level' => env('LOG_LEVEL', 'critical'),
            'replace_placeholders' => true,
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => env('LOG_PAPERTRAIL_HANDLER', SyslogUdpHandler::class),
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
                'connectionString' => 'tls://'.env('PAPERTRAIL_URL').':'.env('PAPERTRAIL_PORT'),
            ],
            'processors' => [PsrLogMessageProcessor::class],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'handler_with' => [
                'stream' => 'php://stderr',
            ],
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'processors' => [PsrLogMessageProcessor::class],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
            'facility' => env('LOG_SYSLOG_FACILITY', \LOG_USER),
            'replace_placeholders' => true,
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

        /**
         * @see https://github.com/hamidrezaniazi/pecs
         */
        'ecs' => [
            'driver' => 'single',
            'tap' => [App\Support\Monolog\EcsFormatterTapper::class],
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        /**
         * @see https://laravel-news.com/split-log-levels-between-stdout-and-stderr-with-laravel
         */
        'stdout' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'handler_with' => [
                'stream' => 'php://stdout',
            ],
            'formatter' => env('LOG_STDOUT_FORMATTER'),
            'processors' => [PsrLogMessageProcessor::class],
        ],

        'eloquent-http' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'action_level' => env('LOG_ACTION_LEVEL', 'debug'),
            'stop_buffering' => true,
            'handler' => EloquentHandler::class,
            'handler_with' => [
                'modelClass' => HttpLog::class,
            ],
            'formatter' => EloquentLogHttpModelFormatter::class,
            'formatter_with' => [],
            'processors' => [
                // EloquentLogHttpModelProcessor::class,
                // LoadAverageProcessor::class,
                // [
                //     'processor' => LoadAverageProcessor::class,
                //     'with' => ['avgSystemLoad' => LoadAverageProcessor::LOAD_5_MINUTE],
                // ],
                MemoryPeakUsageProcessor::class,
                // MemoryUsageProcessor::class,
            ],
        ],
    ],

    'query' => [
        'enabled' => env('QUERY_LOG_ENABLED', env('APP_ENV') === 'local'),

        // Only record queries when the QUERY_LOG_TRIGGER is set in the environment,
        // or when the trigger HEADER, GET, POST, or COOKIE variable is set.
        'trigger' => env('QUERY_LOG_TRIGGER'),

        // Only record queries that are slower than the following time
        // Unit: milliseconds
        'slower_than' => env('QUERY_LOG_SLOWER_THAN', 0),

        // Except record queries
        'except' => env_explode(
            'QUERY_LOG_EXCEPT',
            [
                \sprintf(
                    '*%stelescope_*',
                    $prefix = config(\sprintf('database.connections.%s.prefix', config('database.default')))
                ),
                "*{$prefix}admin_*",
            ],
            '|'
        ),

        // Log Channel
        'channel' => env('QUERY_LOG_CHANNEL', 'daily-query'),
    ],
];
