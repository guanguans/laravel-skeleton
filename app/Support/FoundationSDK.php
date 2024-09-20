<?php

/** @noinspection MethodVisibilityInspection */

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
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
 * @property $stubCallbacks
 */
abstract class FoundationSDK
{
    use Conditionable;
    use Dumpable;
    use Macroable;
    use Tappable;

    protected static ?string $userAgent = null;

    protected array $config;

    protected Factory $http;

    protected PendingRequest $pendingRequest;

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
            /** @phpstan-ignore-next-line  */
            $request->stub(fn (): Collection => $this->stubCallbacks->call($this->http));
        });
    }

    /**
     * ```php
     * protected function validateConfig(array $config): array
     * {
     *     return $this->validate($config, [
     *         'http_options' => 'array',
     *     ]);
     * }
     * ```
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    abstract protected function validateConfig(array $config): array;

    /**
     * @throws \Illuminate\Validation\ValidationException
     * @throws BindingResolutionException
     */
    protected function validate(array $data, array $rules, array $messages = [], array $customAttributes = []): array
    {
        return app(\Illuminate\Validation\Factory::class)->make($data, $rules, $messages, $customAttributes)->validate();
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
            ->withMiddleware(Middleware::mapRequest(
                static fn (RequestInterface $request) => $request->withHeader('X-Date-Time', now()->toDateTimeString('m'))
            ))
            ->withMiddleware(Middleware::mapResponse(
                static fn (ResponseInterface $response) => $response->withHeader('X-Date-Time', now()->toDateTimeString('m'))
            ));
    }

    protected function buildLoggerMiddleware(
        null|LoggerInterface|string $logger = null,
        ?MessageFormatter $formatter = null,
        string $logLevel = 'info'
    ): callable {
        if (! $logger instanceof LoggerInterface) {
            $logger = Log::channel($logger);
        }

        if (! $logger instanceof Logger) {
            $logger = new Logger($logger, app(Dispatcher::class));
        }

        $formatter = $formatter ?: new MessageFormatter(MessageFormatter::DEBUG);

        return Middleware::log($logger, $formatter, $logLevel);
    }

    protected function userAgent(): string
    {
        return static::$userAgent ?? static::$userAgent = implode(' ', [
            str(config('app.name'))->append('/'.config('app.version'))->rtrim('/'),
            \sprintf('guzzle/%s', InstalledVersions::getPrettyVersion('guzzlehttp/guzzle')),
            \sprintf('curl/%s', curl_version()['version']),
            \sprintf('PHP/%s', PHP_VERSION),
            \sprintf('%s/%s', PHP_OS, php_uname('r')),
        ]);
    }

    protected function defaultConfig(): array
    {
        return [
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
            'base_url' => '',
        ];
    }
}
