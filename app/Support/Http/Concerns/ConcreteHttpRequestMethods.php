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

namespace App\Support\Http\Concerns;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * Sync client interface for sending HTTP requests.
 */
trait ConcreteHttpRequestMethods
{
    abstract public function request(string $method, string|UriInterface $uri, array $options = []): ResponseInterface;

    public function get(string|UriInterface $uri, array $options = []): ResponseInterface
    {
        return $this->request('GET', $uri, $options);
    }

    public function head(string|UriInterface $uri, array $options = []): ResponseInterface
    {
        return $this->request('HEAD', $uri, $options);
    }

    public function put(string|UriInterface $uri, array $options = []): ResponseInterface
    {
        return $this->request('PUT', $uri, $options);
    }

    public function post(string|UriInterface $uri, array $options = []): ResponseInterface
    {
        return $this->request('POST', $uri, $options);
    }

    public function patch(string|UriInterface $uri, array $options = []): ResponseInterface
    {
        return $this->request('PATCH', $uri, $options);
    }

    public function delete(string|UriInterface $uri, array $options = []): ResponseInterface
    {
        return $this->request('DELETE', $uri, $options);
    }
}
