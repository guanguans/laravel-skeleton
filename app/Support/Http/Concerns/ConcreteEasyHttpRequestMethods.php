<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

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
    abstract public function request(string $method, string|UriInterface $uri, array $options = []): ResponseInterface;

    public function get(string|UriInterface $uri, array|string $query = [], array $options = []): ResponseInterface
    {
        return $this->request('GET', $uri, array_merge_recursive($options, ['query' => $query]));
    }

    public function head(string|UriInterface $uri, array|string $query = [], array $options = []): ResponseInterface
    {
        return $this->request('HEAD', $uri, array_merge_recursive($options, ['query' => $query]));
    }

    public function put(string|UriInterface $uri, array $data = [], array $options = []): ResponseInterface
    {
        return $this->request('PUT', $uri, array_merge_recursive($options, [$this->bodyFormat => $data]));
    }

    public function post(string|UriInterface $uri, array $data = [], array $options = []): ResponseInterface
    {
        return $this->request('POST', $uri, array_merge_recursive($options, [$this->bodyFormat => $data]));
    }

    public function patch(string|UriInterface $uri, array $data = [], array $options = []): ResponseInterface
    {
        return $this->request('PATCH', $uri, array_merge_recursive($options, [$this->bodyFormat => $data]));
    }

    public function delete(string|UriInterface $uri, array $data = [], array $options = []): ResponseInterface
    {
        return $this->request('DELETE', $uri, array_merge_recursive($options, [$this->bodyFormat => $data]));
    }

    public function upload($uri, array $files = [], array $form = [], array $options = [])
    {
        $this->asMultipart();

        $fileMultipart = [];
        foreach ($files as $name => $contents) {
            $contents = \is_resource($contents) ? $contents : fopen($contents, 'r');
            $fileMultipart[] = compact('name', 'contents');
        }

        $formMultipart = [];
        foreach ($form as $name => $contents) {
            $formMultipart[] = $this->normalizeMultipartField($name, $contents);
        }

        $multipart = array_merge($fileMultipart, ...$formMultipart);

        return $this->request('POST', $uri, array_merge_recursive($options, [$this->bodyFormat => $multipart]));
    }

    public function normalizeMultipartField(string $name, mixed $contents): array
    {
        $field = [];

        if (! \is_array($contents)) {
            return [compact('name', 'contents')];
        }

        foreach ($contents as $key => $value) {
            $key = \sprintf('%s[%s]', $name, $key);

            /** @noinspection SlowArrayOperationsInLoopInspection */
            $field = array_merge(
                $field,
                \is_array($value)
                    ? $this->normalizeMultipartField($key, $value)
                    : [['name' => $key, 'contents' => $value]]
            );
        }

        return $field;
    }
}
