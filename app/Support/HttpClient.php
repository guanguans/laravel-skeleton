<?php

declare(strict_types=1);

namespace App\Support;

/**
 * @see https://github.com/deployphp/deployer/blob/master/src/Utility/Httpie.php
 */
class HttpClient
{
    private string $method = 'GET';

    private string $url = '';

    private array $headers = [];

    private string $body = '';

    private array $curlopts = [];

    private bool $nothrow = false;

    public function __construct()
    {
        if (! \extension_loaded('curl')) {
            throw new \RuntimeException(
                "Please, install curl extension.\n".
                'https://goo.gl/yTAeZh'
            );
        }
    }

    public static function get(string $url): static
    {
        $http = new static();
        $http->method = 'GET';
        $http->url = $url;

        return $http;
    }

    public static function post(string $url): static
    {
        $http = new static();
        $http->method = 'POST';
        $http->url = $url;

        return $http;
    }

    public static function patch(string $url): static
    {
        $http = new static();
        $http->method = 'PATCH';
        $http->url = $url;

        return $http;
    }

    public function query(array $params): static
    {
        $http = clone $this;
        $http->url .= '?'.http_build_query($params);

        return $http;
    }

    public function header(string $header, string $value): static
    {
        $http = clone $this;
        $http->headers[$header] = $value;

        return $http;
    }

    public function body(string $body): static
    {
        $http = clone $this;
        $http->body = $body;
        $http->headers = array_merge($http->headers, [
            'Content-Type' => 'application/json',
            'Content-Length' => \strlen($http->body),
        ]);

        return $http;
    }

    public function jsonBody(array $data): static
    {
        $http = clone $this;
        $http->body = json_encode($data, JSON_PRETTY_PRINT);
        $http->headers = array_merge($http->headers, [
            'Content-Type' => 'application/json',
            'Content-Length' => \strlen($http->body),
        ]);

        return $http;
    }

    public function formBody(array $data): static
    {
        $http = clone $this;
        $http->body = http_build_query($data);
        $http->headers = array_merge($this->headers, [
            'Content-type' => 'application/x-www-form-urlencoded',
            'Content-Length' => \strlen($http->body),
        ]);

        return $http;
    }

    /**
     * @param  mixed  $value
     */
    public function setopt(int $setopt, $value): static
    {
        $http = clone $this;
        $http->curlopts[$setopt] = $value;

        return $http;
    }

    public function nothrow(bool $nothrow = true): static
    {
        $http = clone $this;
        $http->nothrow = $nothrow;

        return $http;
    }

    public function send(?array &$info = null): string
    {
        $ch = curl_init($this->url);

        $headers = [];
        foreach ($this->headers as $key => $value) {
            $headers[] = "$key: $value";
        }

        curl_setopt_array($ch, $this->curlopts + [
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_USERAGENT => 'Laravel '.app()->version(),
            CURLOPT_CUSTOMREQUEST => $this->method,
            CURLOPT_POSTFIELDS => $this->body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 5,
        ]);

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);

        if (false === $result && ! $this->nothrow) {
            $error = curl_error($ch);
            $errno = curl_errno($ch);
            curl_close($ch);

            throw new \RuntimeException($error, $errno);
        }

        curl_close($ch);

        return (string) $result;
    }

    public function getJson(): array
    {
        $response = json_decode($this->send(), true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \RuntimeException('JSON Error: '.json_last_error_msg());
        }

        return $response;
    }
}
