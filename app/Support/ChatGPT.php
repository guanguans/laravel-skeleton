<?php

/** @noinspection PhpParamsInspection */

namespace App\Support;

use GuzzleHttp\Middleware;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Psr\Http\Message\RequestInterface;

class ChatGPT extends FoundationSdk
{
    public function refreshAccessToken()
    {
        return Http::withOptions($this->config['options'])
            ->asJson()
            ->withUserAgent($this->config['user_agent'])
            ->withHeaders([
                'cookie' => "__Secure-next-auth.session-token={$this->config['session_token']}",
            ])
            ->get($this->config['auth_url']);
    }

    public function sendMessage(string $message)
    {
        if (! Cache::get(__CLASS__)) {
            Cache::put(__CLASS__, $this->refreshAccessToken()->json('accessToken'));
        }

        $accessToken = Cache::get(__CLASS__);

        return $this->pendingRequest
            ->withHeaders([
                'Authorization' => "Bearer $accessToken",
            ])
            ->post($this->config['conversation_url'], [
                'action' => 'next',
                'messages' => [
                    [
                        'id' => Str::uuid(),
                        'role' => 'user',
                        'content' => [
                            'content_type' => 'text',
                            'parts' => [$message],
                        ],
                    ],
                ],
                'model' => 'text-davinci-002-render',
                'parent_message_id' => Str::uuid(),
            ]);
    }

    protected function validateConfig(array $config): array
    {
        return $this->validateData($config, [
            'options' => 'array',
            'options.allow_redirects' => 'bool|array',
            'options.auth' => 'array|string|nullable',
            'options.body' => 'string|resource|\Psr\Http\Message\StreamInterface',
            'options.cert' => 'string|array',
            'options.cookies' => '\GuzzleHttp\Cookie\CookieJarInterface',
            'options.connect_timeout' => 'numeric',
            'options.debug' => 'bool|resource',
            'options.decode_content' => 'string|bool',
            'options.delay' => 'numeric',
            'options.expect' => 'bool|integer',
            'options.form_params' => 'array',
            'options.headers' => 'array',
            'options.http_errors' => 'bool',
            'options.idn_conversion' => 'bool',
            'options.json' => 'nullable|string|integer|numeric|array|object',
            'options.multipart' => 'array',
            'options.on_headers' => 'callable',
            'options.on_stats' => 'callable',
            'options.proxy' => 'string|array',
            'options.query' => 'array|string',
            'options.sink' => 'string|resource|\Psr\Http\Message\StreamInterface',
            'options.ssl_key' => 'string|array',
            'options.stream' => 'bool',
            'options.synchronous' => 'bool',
            'options.verify' => 'bool|string',
            'options.timeout' => 'numeric',
            'options.version' => 'string|numeric',

            'session_token' => 'required|string',

        ]) +
               [
                   'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36',
                   'conversation_url' => 'https://chat.openai.com/backend-api/conversation',
                   'auth_url' => 'https://chat.openai.com/api/auth/session',
               ];
    }

    protected function buildPendingRequest(array $config): PendingRequest
    {
        return Http::withOptions($config['options'])
            // ->baseUrl($config['base_url'])
            ->asJson()
            ->withUserAgent($this->config['user_agent'])
            // ->withOptions([
            //     'json' => $data = [
            //         'token' => $config['token'],
            //         'pushkey' => $config['key'],
            //     ],
            //     'form_params' => $data,
            //     'query' => $data,
            // ])
            ->withMiddleware(Middleware::mapRequest(function (RequestInterface $request) {
                return $request->withHeader('X-Date-Time', now()->toDateTimeString());
            }));
    }
}
