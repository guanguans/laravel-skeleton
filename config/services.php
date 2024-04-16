<?php

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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'signer' => [
        'default' => [
            'secret' => env('SIGNER_DEFAULT_SECRET', ''),
            'algo' => env('SIGNER_DEFAULT_ALGO', 'sha256'),
        ],
    ],

    'chatgpt' => [
        'http_options' => [
            'connect_timeout' => 3,
            'timeout' => 60,
        ],
        'session_token' => env('CHATGPT_SESSION_TOKEN', ''),
    ],

    'openai' => [
        'http_options' => [
            'connect_timeout' => 3,
            'timeout' => 60,
        ],
        'api_key' => env('OPENAI_API_KEY', ''),
    ],

    'pushdeer' => [
        'http_options' => [
            'connect_timeout' => 3,
            'timeout' => 30,
        ],
        'base_url' => env('PUSHDEER_BASE_URL', 'https://api2.pushdeer.com'),
        'key' => env('PUSHDEER_KEY'),
        'token' => env('PUSHDEER_TOKEN', 'token'),
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

    'pushbullet' => [
        'access_token' => env('PUSHBULLET_ACCESS_TOKEN'),
    ],

    'dependency_injection' => [
        'only' => [
            'App\*',
        ],
        'except' => [
            'App\Support\Macros\*',
        ],
    ],
];
