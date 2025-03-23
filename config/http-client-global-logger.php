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
    | Enable logging
    |--------------------------------------------------------------------------
    |
    | Whether or not logging should be enabled/disabled.
    |
    */
    'enabled' => env('HTTP_CLIENT_GLOBAL_LOGGER_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Mixin variant
    |--------------------------------------------------------------------------
    |
    | Mixin variant enables Http:log($name) feature on Laravel HTTP Client instead
    | of globally logging everything. This will turn off global logging and only
    | log requests/responses that have been initiated by this method call.
    | The only advantage of using this variant is, that you can provide a log
    | channel name.
    |
    */
    'mixin' => env('HTTP_CLIENT_GLOBAL_LOGGER_MIXIN', false),

    /*
    |--------------------------------------------------------------------------
    | Log to channel
    |--------------------------------------------------------------------------
    |
    | Use the default log channel 'http-client' if you don't want to provide
    | your own. Logging to 'default' channel is not supported as this channel
    | needs to use monolog driver.
    |
    */
    'channel' => env('HTTP_CLIENT_GLOBAL_LOGGER_CHANNEL', 'http-client'),

    /*
    |--------------------------------------------------------------------------
    | Logfile
    |--------------------------------------------------------------------------
    |
    | This is only applied to default log channel 'http-client'.
    |
    */
    'logfile' => env('HTTP_CLIENT_GLOBAL_LOGGER_LOGFILE', storage_path('logs/http-client.log')),

    /*
    |--------------------------------------------------------------------------
    | Log message formats
    |--------------------------------------------------------------------------
    |
    | The following variable substitutions are supported:
    |
    | - {request}:        Full HTTP request message
    | - {response}:       Full HTTP response message
    | - {ts}:             ISO 8601 date in GMT
    | - {date_iso_8601}   ISO 8601 date in GMT
    | - {date_common_log} Apache common log date using the configured timezone.
    | - {host}:           Host of the request
    | - {method}:         Method of the request
    | - {uri}:            URI of the request
    | - {version}:        Protocol version
    | - {target}:         Request target of the request (path + query + fragment)
    | - {hostname}:       Hostname of the machine that sent the request
    | - {code}:           Status code of the response (if available)
    | - {phrase}:         Reason phrase of the response  (if available)
    | - {error}:          Any error messages (if available)
    | - {req_header_*}:   Replace `*` with the lowercased name of a request header to add to the message
    | - {res_header_*}:   Replace `*` with the lowercased name of a response header to add to the message
    | - {req_headers}:    Request headers
    | - {res_headers}:    Response headers
    | - {req_body}:       Request body
    | - {res_body}:       Response body
    |
    | (see https://github.com/guzzle/guzzle/blob/master/src/MessageFormatter.php)
    |
    */
    'format' => [
        'request' => env('HTTP_CLIENT_GLOBAL_LOGGER_REQUEST_FORMAT',
            "REQUEST: {method} {uri}\r\n{req_headers}\r\n{req_body}"
        ),
        'response' => env('HTTP_CLIENT_GLOBAL_LOGGER_RESPONSE_FORMAT',
            "RESPONSE: HTTP/{version} {code} {phrase}\r\n{res_headers}\r\n{res_body}\r\n"
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Obfuscation of credentials in logs
    |--------------------------------------------------------------------------
    |
    | Obfuscation only works if you have disabled 'mixin' above, which is the default.
    */
    'obfuscate' => [
        'enabled' => env('HTTP_CLIENT_GLOBAL_LOGGER_OBFUSCATE_ENABLED', true),
        'replacement' => env('HTTP_CLIENT_GLOBAL_LOGGER_OBFUSCATE_REPLACEMENT', '**********'),
        'headers' => explode(',', env(
            'HTTP_CLIENT_GLOBAL_LOGGER_OBFUSCATE_HEADERS',
            'Authorization'
        )),
        'body_keys' => explode(',', env(
            'HTTP_CLIENT_GLOBAL_LOGGER_OBFUSCATE_BODY_KEYS',
            'pass,password,token,apikey,access_token,refresh_token,client_secret'
        )),
    ],

    /*
    |--------------------------------------------------------------------------
    | Trim response body
    |--------------------------------------------------------------------------
    |
    | Trim response body to a certain length. This is useful when you are logging
    | large responses, and you don't want to fill up your log files.
    |
    | NOTE the leading comma in trim_response_body.content_type_whitelist default value:
    | it's there to whitelist empty content types (e.g. when no Content-Type header is set).
    |
    | The content type whitelisting can be ignored by setting the following header in the
    | request: X-Global-Logger-Trim-Always (set it to any value, e.g. 'true').
    */
    'trim_response_body' => [
        'enabled' => env('HTTP_CLIENT_GLOBAL_LOGGER_TRIM_RESPONSE_BODY_ENABLED', false),
        'limit' => env('HTTP_CLIENT_GLOBAL_LOGGER_TRIM_RESPONSE_BODY_LIMIT', 200),
        'content_type_whitelist' => explode(',', env(
            'HTTP_CLIENT_GLOBAL_LOGGER_TRIM_RESPONSE_BODY_CONTENT_TYPE_WHITELIST',
            ',application/json'
        )),
    ],
];
