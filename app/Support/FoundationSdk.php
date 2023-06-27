<?php

declare(strict_types=1);

namespace App\Support;

use Composer\InstalledVersions;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\MessageFormatterInterface;
use GuzzleHttp\Middleware;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Stringable;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;
use Psr\Log\LoggerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\VarDumper\VarDumper;

abstract class FoundationSdk
{
    use Conditionable;
    use Macroable;
    use Tappable;

    protected array $config;

    protected \Illuminate\Http\Client\Factory $http;

    protected \Illuminate\Http\Client\PendingRequest $defaultPendingRequest;

    public function __construct(array $config)
    {
        $this->config = $this->validateConfig($config);
        $this->http = Http::getFacadeRoot();
        $this->defaultPendingRequest = $this->buildDefaultPendingRequest($this->config);
    }

    public function dd()
    {
        return $this->tapDefaultPendingRequest(static function (PendingRequest $pendingRequest): void {
            $pendingRequest->dd();
        });
    }

    public function dump()
    {
        return $this->tapDefaultPendingRequest(static function (PendingRequest $pendingRequest): void {
            $pendingRequest->dump();
        });
    }

    public function ddRequestData()
    {
        return $this->tapDefaultPendingRequest(static function (PendingRequest $pendingRequest): void {
            $pendingRequest->beforeSending(static function (Request $request, array $options): void {
                VarDumper::dump($options['laravel_data']);

                exit(1);
            });
        });
    }

    public function dumpRequestData()
    {
        return $this->tapDefaultPendingRequest(static function (PendingRequest $pendingRequest): void {
            $pendingRequest->beforeSending(static function (Request $request, array $options): void {
                VarDumper::dump($options['laravel_data']);
            });
        });
    }

    public function withLoggerMiddleware(?LoggerInterface $logger = null, ?MessageFormatterInterface $formatter = null, string $logLevel = 'info')
    {
        return $this->tapDefaultPendingRequest(function (PendingRequest $pendingRequest) use ($logLevel, $formatter, $logger): void {
            $pendingRequest->withMiddleware($this->buildLoggerMiddleware($logger, $formatter, $logLevel));
        });
    }

    public function buildLoggerMiddleware(?LoggerInterface $logger = null, ?MessageFormatterInterface $formatter = null, string $logLevel = 'info'): callable
    {
        $logger = $logger ?: Log::channel('daily');
        $formatter = $formatter ?: new MessageFormatter(MessageFormatter::DEBUG);

        return Middleware::log($logger, $formatter, $logLevel);
    }

    public function tapDefaultPendingRequest(callable $callback)
    {
        $this->defaultPendingRequest = tap($this->defaultPendingRequest, $callback);

        return $this;
    }

    /**
     * @psalm-suppress UndefinedThisPropertyFetch
     *
     * @noinspection PhpUndefinedFieldInspection
     */
    public function cloneDefaultPendingRequest(): PendingRequest
    {
        return tap(clone $this->defaultPendingRequest, function (PendingRequest $request): void {
            $getStubCallbacks = fn (): Collection => $this->stubCallbacks;

            $request->stub($getStubCallbacks->call($this->http));
        });
    }

    /**
     * ```php
     * protected function buildDefaultPendingRequest(array $config): PendingRequest
     * {
     *     return parent::buildDefaultPendingRequest($config)
     *         ->baseUrl($config['baseUrl'])
     *         ->asJson()
     *         ->withMiddleware($this->buildLogMiddleware());
     * }
     * ```.
     */
    protected function buildDefaultPendingRequest(array $config): PendingRequest
    {
        return $this->http->withUserAgent($this->userAgent());
    }

    protected function userAgent(): string
    {
        static $userAgent;

        if (null === $userAgent) {
            $userAgent = implode(' ', [
                sprintf('laravel/%s', str(app()->version())->whenStartsWith('v', static fn (Stringable $version): Stringable => $version->replaceFirst('v', ''))),
                sprintf('guzzle/%s', InstalledVersions::getPrettyVersion('guzzlehttp/guzzle')),
                sprintf('curl/%s', curl_version()['version']),
                sprintf('PHP/%s', PHP_VERSION),
                sprintf('%s/%s', PHP_OS, php_uname('r')),
            ]);
        }

        return $userAgent;
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validate(array $data = [], array $rules = [], array $messages = [], array $customAttributes = []): array
    {
        return validator($data, $rules, $messages, $customAttributes)->validate();
    }

    /**
     * ```php
     * protected function validateConfig(array $config): array
     * {
     *     return configure_options($config, function (OptionsResolver $optionsResolver) {
     *         $optionsResolver
     *             ->setDefined('http_options')
     *             ->setDefault('http_options', [])
     *             ->addAllowedTypes('http_options', 'array');
     *     });
     * }
     * ```
     *
     * ```php
     * protected function validateConfig(array $config): array
     * {
     *     return $this->validate($config, [
     *         'http_options' => 'array',
     *     ]);
     * }
     * ```
     *
     * @return array The merged and validated options
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException If an option name is undefined
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException If an option doesn't fulfill the specified validation rules
     * @throws \Symfony\Component\OptionsResolver\Exception\MissingOptionsException If a required option is missing
     * @throws \Symfony\Component\OptionsResolver\Exception\OptionDefinitionException If there is a cyclic dependency between lazy options and/or normalizers
     * @throws \Symfony\Component\OptionsResolver\Exception\NoSuchOptionException If a lazy option reads an unavailable option
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException If called from a lazy option or normalizer
     * @throws \Illuminate\Validation\ValidationException laravel validation rules
     */
    abstract protected function validateConfig(array $config): array;
}
