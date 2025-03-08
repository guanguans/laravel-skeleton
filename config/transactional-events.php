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
    | Transactional Events Configuration
    |--------------------------------------------------------------------------
    |
    | This file is for configuring the transactional events of your application
    | that should be dispatched if and only if the outer transaction commits.
    | You can enable event namespaces using prefixes such as App\\ as well
    | as setting up events that should not have a transactional behavior.
    |
    */

    'enable' => false,

    'transactional' => [
        'App\Events',
    ],

    'excluded' => [
        // 'eloquent.*',
        'eloquent.booted',
        'eloquent.retrieved',
        'eloquent.saved',
        'eloquent.updated',
        'eloquent.created',
        'eloquent.deleted',
        'eloquent.restored',
    ],
];
