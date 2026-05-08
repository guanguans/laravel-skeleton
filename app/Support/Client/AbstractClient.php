<?php

/** @noinspection PhpUnusedAliasInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Support\Client;

use App\Listeners\PrepareRequestListener;
use Composer\InstalledVersions;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\RetryMiddleware;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Dumpable;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Support\Traits\Tappable;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @property list<string> $allowedStrayRequestUrls
 * @property \Illuminate\Support\Collection<int, \Closure(Request $request, array<string, mixed> $options): ResponseInterface> $stubCallbacks
 *
 * @mixin \Illuminate\Http\Client\PendingRequest
 */
abstract class AbstractClient
{
    use Conditionable;
    use Dumpable;
    use ForwardsCalls;
    use Tappable;
    protected readonly Repository $configRepository;
    private ?string $userAgent = null;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config)
    {
        $this->configRepository = new Repository($this->validateConfig($config));
    }

    /**
     * @see \Illuminate\Http\Client\Factory::__call()
     * @see \Spatie\QueryBuilder\QueryBuilder::__call()
     *
     * @param array<int|string, mixed> $arguments
     *
     * @noinspection PhpUndefinedNamespaceInspection
     * @noinspection OverrideMissingInspection
     */
    public function __call(string $name, array $arguments): mixed
    {
        return $this->forwardCallTo($this->pendingRequest(), $name, $arguments);
    }

    public function ddPendingRequest(mixed ...$args): static
    {
        $this->pendingRequest()->dd(...$args);

        return $this;
    }

    public function dumpPendingRequest(mixed ...$args): static
    {
        $this->pendingRequest()->dump(...$args);

        return $this;
    }

    /**
     * @param null|callable(PendingRequest $pendingRequest): void $callback
     */
    public function pendingRequest(?callable $callback = null): PendingRequest
    {
        return tap($this->configureDefaultPendingRequest($this->defaultPendingRequest()), $callback ?? static fn (): null => null);
    }

    abstract protected function configureDefaultPendingRequest(PendingRequest $pendingRequest): PendingRequest;

    /**
     * @param array<string, mixed> $data
     * @param array<string, (\Closure(string $attribute, mixed $value, \Closure $fail): void)|list<mixed>|Rule|string|\Stringable|ValidationRule> $rules
     * @param array<string, string> $messages
     * @param array<string, string> $customAttributes
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return array<string, mixed>
     */
    protected function validate(array $data, array $rules, array $messages = [], array $customAttributes = []): array
    {
        return validator($data, $rules, $messages, $customAttributes)->validate();
    }

    protected function requestId(): ?string
    {
        return \defined('TRACE_ID') ? TRACE_ID : null;
    }

    /**
     * @return array<string, scalar>
     *
     * @noinspection OffsetOperationsInspection
     */
    protected function userAgentItems(): array
    {
        return [
            'laravel' => InstalledVersions::getPrettyVersion('laravel/framework'),
            'guzzle' => InstalledVersions::getPrettyVersion('guzzlehttp/guzzle'),
            'curl' => (curl_version() ?: ['version' => 'unknown'])['version'],
            'PHP' => \PHP_VERSION,
            \PHP_OS => php_uname('r'),
        ];
    }

    /**
     * @return array<string, (\Closure(string $attribute, mixed $value, \Closure $fail): void)|list<mixed>|Rule|string|\Stringable|ValidationRule>
     */
    abstract protected function configRules(): array;

    /**
     * @return array<string, string>
     */
    protected function configMessages(): array
    {
        return [];
    }

    /**
     * @return array<string, string>
     */
    protected function configAttributes(): array
    {
        return [];
    }

    /**
     * @see \Illuminate\Http\Client\Factory::createPendingRequest()
     */
    private function defaultPendingRequest(): PendingRequest
    {
        return Http::baseUrl($this->configRepository->get('base_url'))
            // ->stub((fn (): Collection => $this->stubCallbacks)->call(Http::getFacadeRoot()))
            // ->preventStrayRequests(Http::preventingStrayRequests())
            // ->allowStrayRequests((fn (): array => $this->allowedStrayRequestUrls)->call(Http::getFacadeRoot()))
            ->withAttributes($this->configRepository->get('attributes'))
            ->withOptions($this->configRepository->get('http_options'))
            ->retry(
                times: $this->configRepository->get('retry.times'),
                sleepMilliseconds: $this->configRepository->get('retry.sleep'),
                when: $this->configRepository->get('retry.when'),
                throw: $this->configRepository->get('retry.throw')
            )
            ->when(
                $this->userAgent(),
                static fn (PendingRequest $pendingRequest, string $userAgent) => $pendingRequest->withUserAgent($userAgent)
            )
            ->when(
                $this->requestId(),
                static fn (PendingRequest $pendingRequest, string $requestId) => $pendingRequest->withRequestMiddleware(
                    static fn (RequestInterface $request): RequestInterface => $request->withHeader(PrepareRequestListener::X_REQUEST_ID, $requestId)
                )
            )
            ->withRequestMiddleware(static fn (RequestInterface $request): RequestInterface => $request->withHeader('X-Date-Time', now()->toDateTimeString('m')))
            ->withMiddleware(Middleware::log(Log::channel($this->configRepository->get('logger')), new MessageFormatter(MessageFormatter::DEBUG)))
            ->withResponseMiddleware(static fn (ResponseInterface $response): ResponseInterface => $response->withHeader('X-Date-Time', now()->toDateTimeString('m')))
            ->when(
                $this->requestId(),
                static fn (PendingRequest $pendingRequest, string $requestId) => $pendingRequest->withResponseMiddleware(
                    static fn (ResponseInterface $response): ResponseInterface => $response->withHeader(PrepareRequestListener::X_REQUEST_ID, $requestId)
                )
            );
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return array<string, mixed>
     */
    private function validateConfig(array $config): array
    {
        return $this->validate(
            array_replace_recursive($this->defaultConfig(), $config),
            $this->configRules() + $this->defaultConfigRules(),
            $this->configMessages(),
            $this->configAttributes()
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function defaultConfig(): array
    {
        return [
            // 'base_url' => null,
            'logger' => 'null',
            'attributes' => [],
            'http_options' => [
                // RequestOptions::CONNECT_TIMEOUT => 10,
                // RequestOptions::TIMEOUT => 30,
            ],
            /**
             * @see \GuzzleHttp\RetryMiddleware::exponentialDelay()
             * @see \retry()
             * @see PendingRequest::$tries
             * @see PendingRequest::retry()
             */
            'retry' => [
                'times' => [RetryMiddleware::exponentialDelay(1)],
                'sleep' => 1000,
                // 'when' => static fn (\Throwable $throwable): bool => $throwable instanceof ConnectException,
                'when' => null,
                'throw' => true,
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function defaultConfigRules(): array
    {
        return [
            'base_url' => 'required|string',
            'logger' => 'nullable|string',
            'attributes' => 'array',
            'http_options' => 'array',
            'retry' => 'array',
        ];
    }

    private function userAgent(): string
    {
        return $this->userAgent ??= collect($this->userAgentItems())
            ->map(static fn (mixed $value, string $name): string => "$name/$value")
            ->implode(' ');
    }
}
