<?php

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

return [
    'enabled' => (bool) env('API_DEBUGGER_ENABLED', env('APP_DEBUG', false)),
    /**
     * Specify what data to collect.
     */
    'collections' => [
        // Database queries.
        \Lanin\Laravel\ApiDebugger\Collections\QueriesCollection::class,

        // Show cache events.
        \Lanin\Laravel\ApiDebugger\Collections\CacheCollection::class,

        // Profile custom events.
        \Lanin\Laravel\ApiDebugger\Collections\ProfilingCollection::class,

        // Memory usage.
        \Lanin\Laravel\ApiDebugger\Collections\MemoryCollection::class,
    ],

    'response_key' => env('API_DEBUGGER_KEY', 'debug'),
];
