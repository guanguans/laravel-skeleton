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
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'signer' => [
        'default' => [
            'secret' => env('SIGNER_DEFAULT_SECRET', ''),
            'algo' => env('SIGNER_DEFAULT_ALGO', 'sha256'),
        ],
    ],

    'elasticsearch' => [
        'default' => env('ELASTICSEARCH_DEFAULT', 'read'),
        'quiet' => false,
        'logger' => 'daily-elasticsearch',
        /** @see \GuzzleHttp\RequestOptions */
        'httpClientOptions' => [
        ],
        'connections' => [
            'read' => [
                'hosts' => [
                    'local' => env('ELASTICSEARCH_READ_LOCAL_HOST', 'http://127.0.0.1:19200'),
                    env('ELASTICSEARCH_READ_HOST', 'http://127.0.0.1:9200'),
                ],
                'basicAuthentication' => [
                    env('ELASTICSEARCH_READ_USERNAME'),
                    env('ELASTICSEARCH_READ_PASSWORD'),
                ],
            ],
        ],
    ],

    'pushdeer' => [
        'base_url' => env('PUSHDEER_BASE_URL', 'https://api2.pushdeer.com'),
        'key' => env('PUSHDEER_KEY'),
        'logger' => env('PUSHDEER_LOGGER'),
        'http_options' => [
            // RequestOptions::CONNECT_TIMEOUT => 10,
            // RequestOptions::TIMEOUT => 30,
        ],
    ],

    'autowired' => [
        'only' => [
            'App\*',
        ],
        'except' => [
            'App\Support\Macros\*',
        ],
    ],
];
