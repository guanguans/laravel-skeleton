<?php

namespace App\Support;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

use Psr\Http\Message\RequestInterface;

class PushDeer extends FoundationSdk
{
    public function messagePush(string $text, string $desp = '', string $type = ''): Response
    {
        return $this->pendingRequest->post(
            'message/push',
            $this->validateData(
                [
                    'text' => $text,
                    'desp' => $desp,
                    'type' => $type,
                ],
                [
                    'text' => 'required|string',
                    'desp' => 'string',
                    'type' => 'in:text,markdown',
                ]
            )
        );
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

            'key' => 'required|string',
            'base_url' => 'required|url'
        ]);
    }

    protected function initPendingRequest(array $config): PendingRequest
    {
        return Http::withOptions($config['options'])
            ->baseUrl($config['base_url'])
            ->asJson()
            ->withOptions([
                'json' => $data = [
                    'pushkey' => $this->config['key']
                ],
                'form_params' => $data,
                'query' => $data
            ])
            ->withMiddleware(function (callable $handler): callable {
                return function (RequestInterface $request, array $options) use ($handler) {
                    $options['laravel_data']['pushkey'] = $this->config['key'];

                    return $handler($request, $options);
                };
            });
    }
}
