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
     *  A list of files that should be ignored during the discovering process.
     */
    'ignored_files' => [

    ],

    /**
     * The directories where the package should search for structure scouts
     */
    'structure_scout_directories' => [
        app_path(),
    ],

    /*
     *  Configure the cache driver for discoverers
     */
    'cache' => [
        'driver' => \Spatie\StructureDiscoverer\Cache\LaravelDiscoverCacheDriver::class,
        'store' => null,
    ],
];
