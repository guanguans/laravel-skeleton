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

/**
 * Sync client interface for sending HTTP requests.
 *
 * @mixin \App\Support\Http\Client
 */
trait ConcreteConfigMethods
{
    protected string $bodyFormat = 'json';

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

    /**
     * Indicate that JSON should be returned by the server.
     *
     * @return $this
     */
    public function acceptJson()
    {
        return $this->accept('application/json');
    }

    /**
     * Indicate the type of content that should be returned by the server.
     *
     * @return $this
     */
    public function accept(string $contentType)
    {
        return $this->withHeaders(['Accept' => $contentType]);
    }

    /**
     * Add the given headers to the request.
     *
     * @return $this
     */
    public function withHeaders(array $headers)
    {
        $this->config = array_merge_recursive($this->config, [
            'headers' => $headers,
        ]);

        return $this;
    }

    /**
     * Add the given header to the request.
     *
     * @return $this
     */
    public function withHeader(string $name, mixed $value)
    {
        return $this->withHeaders([$name => $value]);
    }
}
