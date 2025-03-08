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

namespace App\Support\Http\Contracts;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * Client interface for sending HTTP requests.
 */
interface ClientInterface
{
    /** The Guzzle major version. */
    public const MAJOR_VERSION = 1;

    /**
     * Send an HTTP request.
     *
     * @param RequestInterface $request Request to send
     * @param array $options request options to apply to the given
     *                       request and to the transfer
     *
     * @throws GuzzleException
     */
    public function send(RequestInterface $request, array $options = []): ResponseInterface;

    /**
     * Create and send an HTTP request.
     *
     * Use an absolute path to override the base path of the client, or a
     * relative path to append to the base path of the client. The URL can
     * contain the query string as well.
     *
     * @param string $method HTTP method
     * @param string|UriInterface $uri URI object or string
     * @param array $options request options to apply
     *
     * @throws GuzzleException
     */
    public function request(string $method, string|UriInterface $uri, array $options = []): ResponseInterface;

    /**
     * Get a client configuration option.
     *
     * These options include default request options of the client, a "handler"
     * (if utilized by the concrete client), and a "base_uri" if utilized by
     * the concrete client.
     *
     * @deprecated ClientInterface::getConfig will be removed in guzzlehttp/guzzle:8.0.
     *
     * @param null|string $option the config option to retrieve
     */
    public function getConfig(?string $option = null): mixed;
}
