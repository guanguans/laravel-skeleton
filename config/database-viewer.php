<?php

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
