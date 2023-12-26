<?php

declare(strict_types=1);

namespace App\Support;

use App\Providers\AppServiceProvider;
use Composer\InstalledVersions;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;
use Psr\Log\LoggerInterface;
use Symfony\Component\VarDumper\VarDumper;

/**
 * @property $stubCallbacks
 */
abstract class FoundationSDK
{
    use Conditionable;
    use Macroable;
    use Tappable;

    protected static ?string $userAgent = null;

    protected array $config;

    protected Factory $http;

    protected PendingRequest $defaultPendingRequest;

    public function __construct(array $config)
    {
        $this->config = $this->validateConfig($config);
        $this->http = Http::getFacadeRoot();
        $this->defaultPendingRequest = $this->buildDefaultPendingRequest($this->config);
    }

    /**
     * @psalm-suppress UnusedClosureParam
     */
    public function ddRequestData(): static
    {
        return $this->tapDefaultPendingRequest(static function (PendingRequest $pendingRequest): void {
            $pendingRequest->beforeSending(static function (Request $request, array $options): never {
                VarDumper::dump($options['laravel_data']);

                exit(1);
            });
        });
    }

    /**
     * @psalm-suppress UnusedClosureParam
     */
    public function dumpRequestData(): static
    {
        return $this->tapDefaultPendingRequest(static function (PendingRequest $pendingRequest): void {
            $pendingRequest->beforeSending(static function (Request $request, array $options): void {
                VarDumper::dump($options['laravel_data']);
            });
        });
    }

    public function buildLogMiddleware(?LoggerInterface $logger = null, ?MessageFormatter $formatter = null, string $logLevel = 'info'): callable
    {
        $logger = $logger ?: Log::channel('daily');
        $formatter = $formatter ?: new MessageFormatter(MessageFormatter::DEBUG);

        return Middleware::log($logger, $formatter, $logLevel);
    }

    public function tapDefaultPendingRequest(callable $callback): static
    {
        $this->defaultPendingRequest = tap($this->defaultPendingRequest, $callback);

        return $this;
    }

    /**
     * @psalm-suppress UndefinedThisPropertyFetch
     */
    public function cloneDefaultPendingRequest(): PendingRequest
    {
        return tap(clone $this->defaultPendingRequest, function (PendingRequest $request): void {
            /** @phpstan-ignore-next-line  */
            $getStubCallbacks = fn (): Collection => $this->stubCallbacks;

            $request->stub($getStubCallbacks->call($this->http));
        });
    }

    /**
     * ```php
     * protected function validateConfig(array $config): array
     * {
     *     return validate($config, [
     *         'http_options' => 'array',
     *     ]);
     * }
     * ```.
     *
     * @return array The merged and validated options
     *
     * @throws \Illuminate\Validation\ValidationException laravel validation rules
     */
    abstract protected function validateConfig(array $config): array;

    /**
     * ```php
     * protected function buildPendingRequest(array $config): PendingRequest
     * {
     *     return Http::withOptions($config['http_options'])
     *         ->baseUrl($config['baseUrl'])
     *         ->asJson()
     *         ->withMiddleware($this->buildLogMiddleware());
     * }
     * ```.
     */
    protected function buildDefaultPendingRequest(array $config): PendingRequest
    {
        return $this
            ->http
            // ->withHeader(AppServiceProvider::REQUEST_ID_NAME, app(AppServiceProvider::REQUEST_ID_NAME))
            ->withUserAgent(static::userAgent());
    }

    protected static function userAgent(): string
    {
        return static::$userAgent ?? static::$userAgent = implode(' ', [
            str(config('app.name'))->append('/'.config('app.version'))->rtrim('/'),
            // sprintf('guzzle/%s', InstalledVersions::getPrettyVersion('guzzlehttp/guzzle')),
            // sprintf('curl/%s', curl_version()['version']),
            // sprintf('PHP/%s', PHP_VERSION),
            // sprintf('%s/%s', PHP_OS, php_uname('r')),
        ]);
    }
}
