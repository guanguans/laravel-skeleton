<?php

namespace App\Support;

use App\Traits\ValidatesData;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Tappable;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class FoundationSdk
{
    use Conditionable;
    use Tappable;
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
        $this->pendingRequest = $this->initPendingRequest($this->config);
    }

    /**
     * @param  callable  $callback
     *
     * @return $this
     */
    public function tapPendingRequest($callback)
    {
        $this->pendingRequest = tap($this->pendingRequest, $callback);

        return $this;
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
     * @param  array  $config
     *
     * @return array The merged and validated options
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException If an option name is undefined
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException   If an option doesn't fulfill the specified validation rules
     * @throws \Symfony\Component\OptionsResolver\Exception\MissingOptionsException   If a required option is missing
     * @throws \Symfony\Component\OptionsResolver\Exception\OptionDefinitionException If there is a cyclic dependency between lazy options and/or normalizers
     * @throws \Symfony\Component\OptionsResolver\Exception\NoSuchOptionException     If a lazy option reads an unavailable option
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException           If called from a lazy option or normalizer
     *
     * @throws \Illuminate\Validation\ValidationException Laravel validation rules.
     */
    abstract protected function validateConfig(array $config): array;

    /**
     *
     * ```php
     * protected function initPendingRequest(array $config): PendingRequest
     * {
     *     return Http::withOptions($config['options'])
     *         // ->dd()
     *         ->baseUrl($config['baseUrl'])
     *         ->asJson()
     *         ->withMiddleware(Middleware::log(Log::channel('daily'), new MessageFormatter(MessageFormatter::DEBUG)));
     * }
     * ```
     *
     * @param  array  $config
     *
     * @return \Illuminate\Http\Client\PendingRequest
     */
    abstract protected function initPendingRequest(array $config): PendingRequest;
}
