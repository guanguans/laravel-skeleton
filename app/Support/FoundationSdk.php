<?php

namespace App\Support;

use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Tappable;
use Symfony\Component\OptionsResolver\Exception\AccessException;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\Exception\NoSuchOptionException;
use Symfony\Component\OptionsResolver\Exception\OptionDefinitionException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class FoundationSdk
{
    use Conditionable;
    use Tappable;

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
        $this->config = $this->validateConfigureOptions($config);
        $this->pendingRequest = $this->buildDefaultPendingRequest($this->config);
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
     * protected function configureOptions(array $config): array
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
     * @param  array  $config
     *
     * @return array The merged and validated options
     *
     * @throws UndefinedOptionsException If an option name is undefined
     * @throws InvalidOptionsException   If an option doesn't fulfill the
     *                                   specified validation rules
     * @throws MissingOptionsException   If a required option is missing
     * @throws OptionDefinitionException If there is a cyclic dependency between
     *                                   lazy options and/or normalizers
     * @throws NoSuchOptionException     If a lazy option reads an unavailable option
     * @throws AccessException           If called from a lazy option or normalizer
     */
    abstract protected function validateConfigureOptions(array $config): array;

    /**
     *
     * ```php
     * protected function buildDefaultPendingRequest(array $config): PendingRequest
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
    abstract protected function buildDefaultPendingRequest(array $config): PendingRequest;
}
