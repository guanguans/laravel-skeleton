<?php

/** @noinspection PhpUnusedAliasInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Support\Clients;

use Composer\InstalledVersions;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use GuzzleHttp\RequestOptions;
use Illuminate\Config\Repository;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Log\Logger;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Dumpable;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Support\Traits\Localizable;
use Illuminate\Support\Traits\Tappable;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * @property \Illuminate\Support\Collection $stubCallbacks
 *
 * @mixin \Illuminate\Http\Client\PendingRequest
 */
abstract class AbstractClient
{
    // use Dumpable;
    use Conditionable;
    use ForwardsCalls;
    use Localizable;
    use Tappable;
    protected Repository $configRepository;
    private ?string $userAgent = null;
    private PendingRequest $pendingRequest;

    public function __construct(array $config)
    {
        $this->configRepository = new Repository($this->validateConfig($config));
        $this->pendingRequest = $this->buildPendingRequest($this->defaultPendingRequest());
    }

    /**
     * @see \Illuminate\Http\Client\Factory::__call()
     * @see \Spatie\QueryBuilder\QueryBuilder::__call()
     *
     * @return mixed|PendingRequest|Response|static
     */
    public function __call(string $name, array $arguments): mixed
    {
        return $this->forwardCallTo($this->pendingRequest(), $name, $arguments);
    }

    public function pendingRequest(?callable $callback = null): PendingRequest
    {
        return tap(
            tap(clone $this->pendingRequest, function (PendingRequest $pendingRequest): void {
                /** @see \Illuminate\Http\Client\Factory::createPendingRequest() */
                $pendingRequest
                    ->stub((fn (): Collection => $this->stubCallbacks)->call(Http::getFacadeRoot()))
                    ->preventStrayRequests(Http::preventingStrayRequests());
            }),
            $callback ?? static fn (): null => null
        );
    }

    abstract protected function buildPendingRequest(PendingRequest $pendingRequest): PendingRequest;

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validate(array $data, array $rules, array $messages = [], array $customAttributes = []): array
    {
        return validator($data, $rules, $messages, $customAttributes)->validate();
    }

    /**
     * @return array<string, scalar>
     */
    protected function userAgent(): array
    {
        return [];
    }

    protected function rules(): array
    {
        return [];
    }

    protected function messages(): array
    {
        return [];
    }

    protected function attributes(): array
    {
        return [];
    }

    private function defaultPendingRequest(): PendingRequest
    {
        return Http::baseUrl($this->configRepository->get('base_url'))
            ->acceptJson()
            ->when(
                \defined('REQUEST_ID'),
                /** @phpstan-ignore-next-line */
                static fn (PendingRequest $pendingRequest) => $pendingRequest->withHeader('X-Request-Id', REQUEST_ID)
            )
            ->when(
                $this->getUserAgent(),
                static fn (PendingRequest $pendingRequest, string $userAgent) => $pendingRequest->withUserAgent($userAgent)
            )
            ->withOptions($this->configRepository->get('http_options'))
            ->retry(
                times: $this->configRepository->get('retry.times'),
                sleepMilliseconds: $this->configRepository->get('retry.sleep'),
                when: $this->configRepository->get('retry.when'),
                throw: $this->configRepository->get('retry.throw')
            )
            ->withMiddleware($this->makeLoggerMiddleware($this->configRepository->get('logger')))
            ->withMiddleware(Middleware::mapRequest(
                static fn (RequestInterface $request) => $request->withHeader('X-Date-Time', now()->toDateTimeString('m'))
            ))
            ->withMiddleware(Middleware::mapResponse(
                static fn (ResponseInterface $response) => $response->withHeader('X-Date-Time', now()->toDateTimeString('m'))
            ));
    }

    private function validateConfig(array $config): array
    {
        return $this->validate(
            array_replace_recursive($this->defaultConfig(), $config),
            $this->rules() + $this->defaultRules(),
            $this->messages(),
            $this->attributes()
        );
    }

    private function defaultConfig(): array
    {
        return [
            'base_url' => null,
            'logger' => null,
            'http_options' => [
                // RequestOptions::CONNECT_TIMEOUT => 10,
                // RequestOptions::TIMEOUT => 30,
            ],
            'retry' => [
                'times' => 1,
                'sleep' => 1000,
                'when' => static fn (\Throwable $throwable): bool => $throwable instanceof ConnectException,
                'throw' => true,
            ],
        ];
    }

    private function defaultRules(): array
    {
        return [
            'base_url' => 'required|string',
            'logger' => 'nullable|string',
            'http_options' => 'array',
            'retry' => 'array',
        ];
    }

    private function getUserAgent(): string
    {
        return $this->userAgent ??= collect($this->userAgent() + $this->defaultUserAgent())
            ->reject(static fn (mixed $value): bool => null === $value)
            ->map(static fn (mixed $value, string $name): string => "$name/$value")
            ->implode(' ');
    }

    /**
     * @noinspection OffsetOperationsInspection
     */
    private function defaultUserAgent(): array
    {
        return [
            'guzzle' => InstalledVersions::getPrettyVersion('guzzlehttp/guzzle'),
            'curl' => (curl_version() ?: ['version' => 'unknown'])['version'],
            'PHP' => \PHP_VERSION,
            \PHP_OS => php_uname('r'),
        ];
    }

    private function makeLoggerMiddleware(
        null|LoggerInterface|string $logger = null,
        ?MessageFormatter $formatter = null,
        string $logLevel = 'info'
    ): callable {
        if (!$logger instanceof LoggerInterface) {
            $logger = Log::channel($logger);
        }

        if (!$logger instanceof Logger) {
            $logger = new Logger($logger, Event::getFacadeRoot()); // @codeCoverageIgnore
        }

        return Middleware::log(
            $logger,
            $formatter ?? new MessageFormatter(MessageFormatter::DEBUG),
            $logLevel
        );
    }
}
