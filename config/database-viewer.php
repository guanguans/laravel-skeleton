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

    /*
    |--------------------------------------------------------------------------
    | Database Viewer Route
    |--------------------------------------------------------------------------
    | Database Viewer will be available under this URL.
    |
    */

    'prefix' => 'database-viewer',

    /*
    |--------------------------------------------------------------------------
    | Database Viewer route middleware.
    |--------------------------------------------------------------------------
    | Optional middleware to use when loading the initial Database Viewer page.
    |
    */

    'middleware' => [
        'web',
        \NextBuild\DatabaseViewer\Http\Middleware\AuthorizeDatabaseViewer::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Viewer API middleware.
    |--------------------------------------------------------------------------
    | Optional middleware to use on every API request. The same API is also
    | used from within the Database Viewer user interface.
    |
    */

    'api_middleware' => [
        \NextBuild\DatabaseViewer\Http\Middleware\AuthorizeDatabaseViewer::class,
        \NextBuild\DatabaseViewer\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    ],

];
