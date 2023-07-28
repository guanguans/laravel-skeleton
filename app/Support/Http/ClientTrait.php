<?php

declare(strict_types=1);

namespace App\Support\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * Client interface for sending HTTP requests.
 */
trait ClientTrait
{
    /**
     * @param  string|UriInterface  $uri     URI object or string
     */
    abstract public function request(string $method, $uri, array $options = []): ResponseInterface;

    /**
     * @param  string|UriInterface  $uri     URI object or string
     */
    public function get($uri, array $options = []): ResponseInterface
    {
        return $this->request('GET', $uri, $options);
    }

    /**
     * @param  string|UriInterface  $uri     URI object or string
     */
    public function head($uri, array $options = []): ResponseInterface
    {
        return $this->request('HEAD', $uri, $options);
    }

    /**
     * @param  string|UriInterface  $uri     URI object or string
     */
    public function put($uri, array $options = []): ResponseInterface
    {
        return $this->request('PUT', $uri, $options);
    }

    /**
     * @param  string|UriInterface  $uri     URI object or string
     */
    public function post($uri, array $options = []): ResponseInterface
    {
        return $this->request('POST', $uri, $options);
    }

    /**
     * @param  string|UriInterface  $uri     URI object or string
     */
    public function patch($uri, array $options = []): ResponseInterface
    {
        return $this->request('PATCH', $uri, $options);
    }

    /**
     * @param  string|UriInterface  $uri     URI object or string
     */
    public function delete($uri, array $options = []): ResponseInterface
    {
        return $this->request('DELETE', $uri, $options);
    }
}
