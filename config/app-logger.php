<?php

return [
    'http' => [
        'enabled' => env('RUN_HTTP_LOG', true),

        /*
         * The log profile which determines whether a request should be logged.
         * It should implement `HttpLogProfile`.
         */
        'log_profile' => \KitLoong\AppLogger\HttpLog\LogProfile::class,

        /*
         * The log writer used to write the request to a log.
         * It should implement `HttpLogWriter`.
         */
        'log_writer' => \KitLoong\AppLogger\HttpLog\LogWriter::class,

        /*
         * If you are using default `HttpLogProfile` provided by the package,
         * you could define which HTTP methods should be logged.
         */
        'should_log' => [
            \Illuminate\Http\Request::METHOD_POST,
            \Illuminate\Http\Request::METHOD_PUT,
            \Illuminate\Http\Request::METHOD_PATCH,
            \Illuminate\Http\Request::METHOD_DELETE,
        ],

        /*
         * Filter out body fields which will never be logged.
         */
        'except' => [
            'password',
            'password_confirmation',
        ],

        /*
         * Log channel name define in config/logging.php
         * null value to use default channel.
         */
        'channel' => null,
    ],

    'performance' => [
        'enabled' => env('RUN_PERFORMANCE_LOG', true),

        /*
         * The log profile which determines whether a request should be logged.
         * It should implement `PerformanceLogProfile`.
         */
        'log_profile' => \KitLoong\AppLogger\PerformanceLog\LogProfile::class,

        /*
         * The log writer used to write the request to a log.
         * It should implement `PerformanceLogWriter`.
         */
        'log_writer' => \KitLoong\AppLogger\PerformanceLog\LogWriter::class,

        /*
         * If you are using default `PerformanceLogProfile` provided by the package,
         * you could define which HTTP methods should be logged.
         */
        'should_log' => [
            \Illuminate\Http\Request::METHOD_GET,
            \Illuminate\Http\Request::METHOD_POST,
            \Illuminate\Http\Request::METHOD_PUT,
            \Illuminate\Http\Request::METHOD_PATCH,
            \Illuminate\Http\Request::METHOD_DELETE,
        ],

        /*
         * Log channel name define in config/logging.php
         * null value to use default channel.
         */
        'channel' => null,
    ],

    'query' => [
        'enabled' => env('RUN_QUERY_LOG', false),

        /*
         * The log profile which determines whether query should be logged.
         * It should implement `QueryLogProfile`.
         */
        'log_profile' => \KitLoong\AppLogger\QueryLog\LogProfile::class,

        /*
         * The log writer used to write the query to a log.
         * It should implement `QueryLogWriter`.
         */
        'log_writer' => \KitLoong\AppLogger\QueryLog\LogWriter::class,

        /*
         * Log channel name define in config/logging.php
         * null value to use default channel.
         */
        'channel' => null,
    ],
];
