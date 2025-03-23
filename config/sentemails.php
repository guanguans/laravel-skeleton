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
    // set the route path to load the sent emails ui defaults to /sentemails
    'routepath' => env('SENT_EMAILS_ROUTE_PATH', 'sentemails'),

    // set the route middlewares to apply on the sent emails ui
    'middleware' => ['web', 'auth'],

    // emails per page
    'perPage' => env('SENT_EMAILS_PER_PAGE', 10),

    'storeAttachments' => env('SENT_EMAILS_STORE_EMAIL', true),

    'noEmailsMessage' => env('SENT_EMAILS_NO_EMAILS_MESSAGE', 'No emails found.'),

    // body emails are stored as compressed strings to save db disk
    /* Do not change after first mail is stored */
    'compressBody' => env('SENT_EMAILS_COMPRESS_BODY', false),
];
