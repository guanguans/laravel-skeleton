<?php

declare(strict_types=1);

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
     *  Automatic registration of routes will only happen if this setting is `true`
     */
    'enabled' => true,

    /*
     * Controllers in these directories that have routing attributes
     * will automatically be registered.
     *
     * Optionally, you can specify group configuration by using key/values
     */
    'directories' => [
        app_path('Http/Controllers'),
        /*
        app_path('Http/Controllers/Api') => [
           'prefix' => 'api',
           'middleware' => 'api',
            // only register routes in files that match the patterns
           'patterns' => ['*Controller.php'],
           // do not register routes in files that match the patterns
           'not_patterns' => [],
        ],
        */
    ],

    /*
     * This middleware will be applied to all routes.
     */
    'middleware' => [
        Illuminate\Routing\Middleware\SubstituteBindings::class,
    ],

    /*
     * When enabled, implicitly scoped bindings will be enabled by default.
     * You can override this behaviour by using the `ScopeBindings` attribute, and passing `false` to it.
     *
     * Possible values:
     *  - null: use the default behaviour
     *  - true: enable implicitly scoped bindings for all routes
     *  - false: disable implicitly scoped bindings for all routes
     */
    'scope-bindings' => null,
];
