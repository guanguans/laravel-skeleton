<?php

declare(strict_types=1);

namespace App\Support\Http\Concerns;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * Sync client interface for sending HTTP requests.
 *
 * @mixin \App\Support\Http\Client
 */
trait ConcreteEasyHttpRequestMethods
{
    protected string $bodyFormat = 'json';

    /**
     * @param  string|UriInterface  $uri
     */
    abstract public function request(string $method, $uri, array $options = []): ResponseInterface;

    /**
     * @param  string|UriInterface  $uri
     */
    public function get($uri, array|string $query = [], array $options = []): ResponseInterface
    {
        return $this->request('GET', $uri, array_merge_recursive($options, ['query' => $query]));
    }

    /**
     * @param  string|UriInterface  $uri
     */
    public function head($uri, array|string $query = [], array $options = []): ResponseInterface
    {
        return $this->request('HEAD', $uri, array_merge_recursive($options, ['query' => $query]));
    }

    /**
     * @param  string|UriInterface  $uri
     */
    public function put($uri, array $data = [], array $options = []): ResponseInterface
    {
        return $this->request('PUT', $uri, array_merge_recursive($options, [$this->bodyFormat => $data]));
    }

    /**
     * @param  string|UriInterface  $uri
     */
    public function post($uri, array $data = [], array $options = []): ResponseInterface
    {
        return $this->request('POST', $uri, array_merge_recursive($options, [$this->bodyFormat => $data]));
    }

    /**
     * @param  string|UriInterface  $uri
     */
    public function patch($uri, array $data = [], array $options = []): ResponseInterface
    {
        return $this->request('PATCH', $uri, array_merge_recursive($options, [$this->bodyFormat => $data]));
    }

    /**
     * @param  string|UriInterface  $uri
     */
    public function delete($uri, array $data = [], array $options = []): ResponseInterface
    {
        return $this->request('DELETE', $uri, array_merge_recursive($options, [$this->bodyFormat => $data]));
    }

    /**
     * Attach a raw body to the request.
     *
     * @return $this
     */
    public function asBody(string $contentType = 'application/json')
    {
        return $this->bodyFormat('body')->contentType($contentType);
    }

    /**
     * Indicate the request contains JSON.
     *
     * @return $this
     */
    public function asJson()
    {
        return $this->bodyFormat('json')->contentType('application/json');
    }

    /**
     * Indicate the request contains form parameters.
     *
     * @return $this
     */
    public function asForm()
    {
        return $this->bodyFormat('form_params')->contentType('application/x-www-form-urlencoded');
    }

    /**
     * Indicate the request is a multi-part form request.
     *
     * @return $this
     */
    public function asMultipart()
    {
        return $this->bodyFormat('multipart');
    }

    /**
     * Specify the body format of the request.
     *
     * @return $this
     */
    public function bodyFormat(string $format)
    {
        $this->bodyFormat = $format;

        return $this;
    }

    /**
     * Specify the request's content type.
     *
     * @return $this
     */
    public function contentType(string $contentType)
    {
        $this->config['headers']['Content-Type'] = $contentType;

        return $this;
    }
}
