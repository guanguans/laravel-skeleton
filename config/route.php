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
    |--------------------------------------------------------------------------
    | Names
    |--------------------------------------------------------------------------
    |
    | This option determines the handling of route names.
    |
    */

    'names' => [
        /*
        |--------------------------------------------------------------------------
        | Exclude Names
        |--------------------------------------------------------------------------
        |
        | This option specifies the names of the routes that will be excluded
        | from the conversion.
        |
        */

        'exclude' => [
            '__clockwork*',
            '_debugbar*',
            '_ignition*',
            'horizon*',
            'pretty-routes*',
            'sanctum*',
            'telescope*',
        ],
    ],
];
