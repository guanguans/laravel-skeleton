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
    'route' => [
        /**
         * Prefix used by the package routes. 'mails' by default.
         */
        'prefix' => env('WEB_MAILER_ROUTE_PREFIX', 'web-inbox'),

        'middleware' => [
            //            'web',
            //            'auth',
        ],
    ],

    /*
     * The path where the emails will be stored
     */
    'storage_path' => storage_path('web-emails'),

    /*
     * To enable this feature, you must schedule
     *  the command: 'laravel-web-mailer:cleanup'
     */
    'delete_emails_older_than_days' => 7,
];
