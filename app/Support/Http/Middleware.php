<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Support\Http;

use GuzzleHttp\BodySummarizerInterface;
use GuzzleHttp\Cookie\CookieJarInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\MessageFormatterInterface;
use GuzzleHttp\PrepareBodyMiddleware;
use GuzzleHttp\Promise as P;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\RedirectMiddleware;
use GuzzleHttp\RetryMiddleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Functions used to create and wrap handlers with handler middleware.
 */
final class Middleware
{
    /**
     * Middleware that adds cookies to requests.
     *
     * The options array must be set to a CookieJarInterface in order to use
     * cookies. This is typically handled for you by a client.
     *
     * @return callable returns a function that accepts the next handler
     */
    public static function cookies(): callable
    {
        return static function (callable $handler): callable {
            return static function ($request, array $options) use ($handler) {
                if (empty($options['cookies'])) {
                    return $handler($request, $options);
                }

                if (!$options['cookies'] instanceof CookieJarInterface) {
                    throw new \InvalidArgumentException('cookies must be an instance of GuzzleHttp\Cookie\CookieJarInterface');
                }

                $cookieJar = $options['cookies'];
                $request = $cookieJar->withCookieHeader($request);

                /** @var ResponseInterface $response */
                $response = $handler($request, $options);
                $cookieJar->extractCookies($request, $response);

                return $response;
            };
        };
    }

    /**
     * Middleware that throws exceptions for 4xx or 5xx responses when the
     * "http_errors" request option is set to true.
     *
     * @param null|BodySummarizerInterface $bodySummarizer the body summarizer to use in exception messages
     *
     * @return callable(callable): callable returns a function that accepts the next handler
     */
    public static function httpErrors(?BodySummarizerInterface $bodySummarizer = null): callable
    {
        return static function (callable $handler) use ($bodySummarizer): callable {
            return static function ($request, array $options) use ($handler, $bodySummarizer) {
                if (empty($options['http_errors'])) {
                    return $handler($request, $options);
                }

                /** @var ResponseInterface $response */
                $response = $handler($request, $options);
                $code = $response->getStatusCode();

                if (400 > $code) {
                    return $response;
                }

                throw RequestException::create($request, $response, null, [], $bodySummarizer);
            };
        };
    }

    /**
     * Middleware that pushes history data to an ArrayAccess container.
     *
     * @param array|\ArrayAccess<int, array> $container container to hold the history (by reference)
     *
     * @throws \InvalidArgumentException if container is not an array or ArrayAccess
     *
     * @return callable(callable): callable returns a function that accepts the next handler
     */
    public static function history(array|\ArrayAccess &$container): callable
    {
        if (!\is_array($container) && !$container instanceof \ArrayAccess) {
            throw new \InvalidArgumentException('history container must be an array or object implementing ArrayAccess');
        }

        return static function (callable $handler) use (&$container): callable {
            return static function (RequestInterface $request, array $options) use ($handler, &$container) {
                return $handler($request, $options)->then(
                    static function ($value) use ($request, &$container, $options) {
                        $container[] = [
                            'request' => $request,
                            'response' => $value,
                            'error' => null,
                            'options' => $options,
                        ];

                        return $value;
                    },
                    static function ($reason) use ($request, &$container, $options) {
                        $container[] = [
                            'request' => $request,
                            'response' => null,
                            'error' => $reason,
                            'options' => $options,
                        ];

                        return P\Create::rejectionFor($reason);
                    }
                );
            };
        };
    }

    /**
     * Middleware that invokes a callback before and after sending a request.
     *
     * The provided listener cannot modify or alter the response. It simply
     * "taps" into the chain to be notified before returning the promise. The
     * before listener accepts a request and options array, and the after
     * listener accepts a request, options array, and response promise.
     *
     * @param callable $before function to invoke before forwarding the request
     * @param callable $after function invoked after forwarding
     *
     * @return callable returns a function that accepts the next handler
     */
    public static function tap(?callable $before = null, ?callable $after = null): callable
    {
        return static function (callable $handler) use ($before, $after): callable {
            return static function (RequestInterface $request, array $options) use ($handler, $before, $after) {
                if ($before) {
                    $before($request, $options);
                }

                $response = $handler($request, $options);

                if ($after) {
                    $after($request, $options, $response);
                }

                return $response;
            };
        };
    }

    /**
     * Middleware that handles request redirects.
     *
     * @return callable returns a function that accepts the next handler
     */
    public static function redirect(): callable
    {
        return static fn (callable $handler): RedirectMiddleware => new RedirectMiddleware($handler);
    }

    /**
     * Middleware that retries requests based on the boolean result of
     * invoking the provided "decider" function.
     *
     * If no delay function is provided, a simple implementation of exponential
     * backoff will be utilized.
     *
     * @param callable $decider function that accepts the number of retries,
     *                          a request, [response], and [exception] and
     *                          returns true if the request is to be retried
     * @param callable $delay function that accepts the number of retries and
     *                        returns the number of milliseconds to delay
     *
     * @return callable returns a function that accepts the next handler
     */
    public static function retry(callable $decider, ?callable $delay = null): callable
    {
        return static fn (callable $handler): RetryMiddleware => new RetryMiddleware($decider, $handler, $delay);
    }

    /**
     * Middleware that logs requests, responses, and errors using a message
     * formatter.
     *
     * @phpstan-param \Psr\Log\LogLevel::* $logLevel  Level at which to log requests.
     *
     * @param LoggerInterface $logger logs messages
     * @param MessageFormatter|MessageFormatterInterface $formatter formatter used to create message strings
     * @param string $logLevel level at which to log requests
     *
     * @return callable returns a function that accepts the next handler
     */
    public static function log(LoggerInterface $logger, MessageFormatter|MessageFormatterInterface $formatter, string $logLevel = 'info'): callable
    {
        // To be compatible with Guzzle 7.1.x we need to allow users to pass a MessageFormatter
        if (!$formatter instanceof MessageFormatter && !$formatter instanceof MessageFormatterInterface) {
            throw new \LogicException(\sprintf('Argument 2 to %s::log() must be of type %s', self::class, MessageFormatterInterface::class));
        }

        return static function (callable $handler) use ($logger, $formatter, $logLevel): callable {
            return static function (RequestInterface $request, array $options = []) use ($handler, $logger, $formatter, $logLevel) {
                return $handler($request, $options)->then(
                    static function ($response) use ($logger, $request, $formatter, $logLevel): ResponseInterface {
                        $message = $formatter->format($request, $response);
                        $logger->log($logLevel, $message);

                        return $response;
                    },
                    static function ($reason) use ($logger, $request, $formatter): PromiseInterface {
                        $response = $reason instanceof RequestException ? $reason->getResponse() : null;
                        $message = $formatter->format($request, $response, P\Create::exceptionFor($reason));
                        $logger->error($message);

                        return P\Create::rejectionFor($reason);
                    }
                );
            };
        };
    }

    /**
     * This middleware adds a default content-type if possible, a default
     * content-length or transfer-encoding header, and the expect header.
     */
    public static function prepareBody(): callable
    {
        return static fn (callable $handler): PrepareBodyMiddleware => new PrepareBodyMiddleware($handler);
    }

    /**
     * Middleware that applies a map function to the request before passing to
     * the next handler.
     *
     * @param callable $fn function that accepts a RequestInterface and returns
     *                     a RequestInterface
     */
    public static function mapRequest(callable $fn): callable
    {
        return static fn (callable $handler): callable => static fn (RequestInterface $request, array $options) => $handler($fn($request), $options);
    }

    /**
     * Middleware that applies a map function to the resolved promise's
     * response.
     *
     * @param callable $fn function that accepts a ResponseInterface and
     *                     returns a ResponseInterface
     */
    public static function mapResponse(callable $fn): callable
    {
        return static fn (callable $handler): callable => static fn (RequestInterface $request, array $options) => $handler($request, $options)->then($fn);
    }
}
