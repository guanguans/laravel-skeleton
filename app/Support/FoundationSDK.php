<?php

/** @noinspection MethodVisibilityInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Support;

use App\Providers\AppServiceProvider;
use Composer\InstalledVersions;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use GuzzleHttp\RequestOptions;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Log\Logger;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Dumpable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\VarDumper\VarDumper;

/**
 * @property \Illuminate\Support\Collection $stubCallbacks
 */
abstract class FoundationSDK
{
    use Conditionable;
    use Dumpable;
    use Macroable;
    use Tappable;
    protected array $config;
    protected Factory $http;
    protected PendingRequest $pendingRequest;
    protected ?string $userAgent = null;

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct(array $config)
    {
        $this->config = array_replace_recursive($this->defaultConfig(), $this->validateConfig($config));
        $this->http = Http::getFacadeRoot();
        $this->pendingRequest = $this->buildPendingRequest($this->config);
    }

    public function ddLaravelData(): static
    {
        return $this->tapPendingRequest(static function (PendingRequest $pendingRequest): void {
            $pendingRequest->beforeSending(static function (Request $request, array $options): never {
                VarDumper::dump($options['laravel_data']);

                exit(1);
            });
        });
    }

    public function dumpLaravelData(): static
    {
        return $this->tapPendingRequest(static function (PendingRequest $pendingRequest): void {
            $pendingRequest->beforeSending(static function (Request $request, array $options): void {
                VarDumper::dump($options['laravel_data']);
            });
        });
    }

    public function tapPendingRequest(callable $callback): static
    {
        $this->pendingRequest = tap($this->pendingRequest, $callback);

        return $this;
    }

    public function clonePendingRequest(): PendingRequest
    {
        return tap(clone $this->pendingRequest, function (PendingRequest $request): void {
            $request->stub((fn (): Collection => $this->stubCallbacks)->call($this->http));
        });
    }

    protected function buildPendingRequest(array $config): PendingRequest
    {
        return $this
            ->http
            ->baseUrl($config['base_url'])
            ->withHeader(AppServiceProvider::REQUEST_ID_NAME, app(AppServiceProvider::REQUEST_ID_NAME))
            ->withUserAgent($this->userAgent())
            ->retry(
                $config['retry']['times'],
                $config['retry']['sleep'],
                $config['retry']['when'],
                $config['retry']['throw']
            )
            ->withOptions($config['http_options'])
            ->withMiddleware($this->buildLoggerMiddleware($config['logger']))
            ->withMiddleware(Middleware::mapRequest(
                static fn (RequestInterface $request) => $request->withHeader('X-Date-Time', now()->toDateTimeString('m'))
            ))
            ->withMiddleware(Middleware::mapResponse(
                static fn (ResponseInterface $response) => $response->withHeader('X-Date-Time', now()->toDateTimeString('m'))
            ));
    }

    protected function defaultConfig(): array
    {
        return [
            'base_url' => '',
            'logger' => null,
            'http_options' => [
                // RequestOptions::CONNECT_TIMEOUT => 10,
                // RequestOptions::TIMEOUT => 30,
            ],
            'retry' => [
                'times' => 1,
                'sleep' => 1000,
                'when' => static fn (\Throwable $e): bool => $e instanceof ConnectException,
                'throw' => true,
            ],
        ];
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function validateConfig(array $config): array
    {
        return $this->validate($config, $this->rules(), $this->messages(), $this->attributes());
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     * @throws BindingResolutionException
     */
    protected function validate(array $data, array $rules, array $messages = [], array $customAttributes = []): array
    {
        return app(\Illuminate\Validation\Factory::class)->make($data, $rules, $messages, $customAttributes)->validate();
    }

    protected function rules(): array
    {
        return [
            'http_options' => 'array',
            'retry' => 'array',
            'base_url' => 'string',
        ];
    }

    protected function messages(): array
    {
        return [];
    }

    protected function attributes(): array
    {
        return [];
    }

    protected function buildLoggerMiddleware(
        null|LoggerInterface|string $logger = null,
        ?MessageFormatter $formatter = null,
        string $logLevel = 'info'
    ): callable {
        if (!$logger instanceof LoggerInterface) {
            $logger = Log::channel($logger);
        }

        if (!$logger instanceof Logger) {
            $logger = new Logger($logger, app(Dispatcher::class));
        }

        $formatter = $formatter ?: new MessageFormatter(MessageFormatter::DEBUG);

        return Middleware::log($logger, $formatter, $logLevel);
    }

    protected function userAgent(): string
    {
        return $this->userAgent ?? $this->userAgent = implode(' ', [
            str(config('app.name'))->append('/'.config('app.version'))->rtrim('/'),
            // \sprintf('guzzle/%s', InstalledVersions::getPrettyVersion('guzzlehttp/guzzle')),
            // \sprintf('curl/%s', curl_version()['version']),
            // \sprintf('PHP/%s', PHP_VERSION),
            // \sprintf('%s/%s', PHP_OS, php_uname('r')),
        ]);
    }
}
