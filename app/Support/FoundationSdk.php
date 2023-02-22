<?php

namespace App\Support;

use App\Traits\ValidatesData;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\MessageFormatterInterface;
use GuzzleHttp\Middleware;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;
use Psr\Log\LoggerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\VarDumper\VarDumper;

abstract class FoundationSdk
{
    use Conditionable;
    use Tappable;
    use Macroable;
    use ValidatesData;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var \Illuminate\Http\Client\PendingRequest
     */
    protected $pendingRequest;

    public function __construct(array $config)
    {
        $this->config = $this->validateConfig($config);
        $this->pendingRequest = $this->buildPendingRequest($this->config);
    }

    public function dd()
    {
        return $this->tapPendingRequest(function (PendingRequest $pendingRequest) {
            $pendingRequest->dd();
        });
    }

    public function dump()
    {
        return $this->tapPendingRequest(function (PendingRequest $pendingRequest) {
            $pendingRequest->dump();
        });
    }

    public function ddRequestData()
    {
        return $this->tapPendingRequest(function (PendingRequest $pendingRequest) {
            $pendingRequest->beforeSending(function (Request $request, array $options) {
                VarDumper::dump($options['laravel_data']);
                exit(1);
            });
        });
    }

    public function dumpRequestData()
    {
        return $this->tapPendingRequest(function (PendingRequest $pendingRequest) {
            $pendingRequest->beforeSending(function (Request $request, array $options) {
                VarDumper::dump($options['laravel_data']);
            });
        });
    }

    public function withLoggerMiddleware(?LoggerInterface $logger = null, ?MessageFormatterInterface $formatter = null, string $logLevel = 'info')
    {
        return $this->tapPendingRequest(function (PendingRequest $pendingRequest) use ($logLevel, $formatter, $logger) {
            $pendingRequest->withMiddleware($this->buildLoggerMiddleware($logger, $formatter, $logLevel));
        });
    }

    public function tapPendingRequest(callable $callback)
    {
        $this->pendingRequest = tap($this->pendingRequest, $callback);

        return $this;
    }

    public static function buildLoggerMiddleware(?LoggerInterface $logger = null, ?MessageFormatterInterface $formatter = null, string $logLevel = 'info'): callable
    {
        $logger = $logger ?: Log::channel('daily');
        $formatter = $formatter ?: new MessageFormatter(MessageFormatter::DEBUG);

        return Middleware::log($logger, $formatter, $logLevel);
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
     *     return $this->validateData($config, [
     *         'http_options' => 'array',
     *     ]);
     * }
     * ```
     *
     * @return array The merged and validated options
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException If an option name is undefined
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException   If an option doesn't fulfill the specified validation rules
     * @throws \Symfony\Component\OptionsResolver\Exception\MissingOptionsException   If a required option is missing
     * @throws \Symfony\Component\OptionsResolver\Exception\OptionDefinitionException If there is a cyclic dependency between lazy options and/or normalizers
     * @throws \Symfony\Component\OptionsResolver\Exception\NoSuchOptionException     If a lazy option reads an unavailable option
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException           If called from a lazy option or normalizer
     * @throws \Illuminate\Validation\ValidationException Laravel validation rules.
     */
    abstract protected function validateConfig(array $config): array;

    /**
     * ```php
     * protected function buildPendingRequest(array $config): PendingRequest
     * {
     *     return Http::withOptions($config['http_options'])
     *         ->baseUrl($config['baseUrl'])
     *         ->asJson()
     *         ->withMiddleware($this->buildLoggerMiddleware());
     * }
     * ```
     */
    abstract protected function buildPendingRequest(array $config): PendingRequest;
}
