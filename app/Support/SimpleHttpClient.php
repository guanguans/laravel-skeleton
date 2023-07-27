<?php

declare(strict_types=1);

namespace App\Support;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class SimpleHttpClient implements ClientInterface
{
    /**
     * 发送HTTP请求
     *
     * @param  RequestInterface  $request HTTP请求对象
     * @return ResponseInterface HTTP响应对象
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface 如果发送请求失败，则抛出异常
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $context = $this->createContext($request);
        $response = file_get_contents($request->getUri(), false, $context);
        $statusLine = strtok($response, "\r\n");
        $headers = [];
        while ($header = trim(strtok("\r\n"))) {
            $headers[] = $header;
        }
        $body = substr($response, strpos($response, "\r\n\r\n") + 4);

        return new \Psr\Http\Message\Response($statusLine, $headers, $body);
    }

    /**
     * 发送GET请求
     *
     * @param  string  $uri 请求的URI
     * @param  array  $headers 请求头部信息
     * @return ResponseInterface HTTP响应对象
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface 如果发送请求失败，则抛出异常
     */
    public function get(string $uri, array $headers = []): ResponseInterface
    {
        $request = new \Psr\Http\Message\Request('GET', $uri, $headers);

        return $this->sendRequest($request);
    }

    /**
     * 发送POST请求
     *
     * @param  string  $uri 请求的URI
     * @param  array|\Psr\Http\Message\StreamInterface|string  $body 请求数据
     * @param  array  $headers 请求头部信息
     * @return ResponseInterface HTTP响应对象
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface 如果发送请求失败，则抛出异常
     */
    public function post(string $uri, $body = null, array $headers = []): ResponseInterface
    {
        $request = new \Psr\Http\Message\Request('POST', $uri, $headers, $body);

        return $this->sendRequest($request);
    }

    /**
     * 发送PUT请求
     *
     * @param  string  $uri 请求的URI
     * @param  array|\Psr\Http\Message\StreamInterface|string  $body 请求数据
     * @param  array  $headers 请求头部信息
     * @return ResponseInterface HTTP响应对象
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface 如果发送请求失败，则抛出异常
     */
    public function put(string $uri, $body = null, array $headers = []): ResponseInterface
    {
        $request = new \Psr\Http\Message\Request('PUT', $uri, $headers, $body);

        return $this->sendRequest($request);
    }

    /**
     * 发送PATCH请求
     *
     * @param  string  $uri 请求的URI
     * @param  array|\Psr\Http\Message\StreamInterface|string  $body 请求数据
     * @param  array  $headers 请求头部信息
     * @return ResponseInterface HTTP响应对象
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface 如果发送请求失败，则抛出异常
     */
    public function patch(string $uri, $body = null, array $headers = []): ResponseInterface
    {
        $request = new \Psr\Http\Message\Request('PATCH', $uri, $headers, $body);

        return $this->sendRequest($request);
    }

    /**
     * 发送DELETE请求
     *
     * @param  string  $uri 请求的URI
     * @param  array  $headers 请求头部信息
     * @return ResponseInterface HTTP响应对象
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface 如果发送请求失败，则抛出异常
     */
    public function delete(string $uri, array $headers = []): ResponseInterface
    {
        $request = new \Psr\Http\Message\Request('DELETE', $uri, $headers);

        return $this->sendRequest($request);
    }

    /**
     * 发送HEAD请求
     *
     * @param  string  $uri 请求的URI
     * @param  array  $headers 请求头部信息
     * @return ResponseInterface HTTP响应对象
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface 如果发送请求失败，则抛出异常
     */
    public function head(string $uri, array $headers = []): ResponseInterface
    {
        $request = new \Psr\Http\Message\Request('HEAD', $uri, $headers);

        return $this->sendRequest($request);
    }

    /**
     * 发送OPTIONS请求
     *
     * @param  string  $uri 请求的URI
     * @param  array  $headers 请求头部信息
     * @return ResponseInterface HTTP响应对象
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface 如果发送请求失败，则抛出异常
     */
    public function options(string $uri, array $headers = []): ResponseInterface
    {
        $request = new \Psr\Http\Message\Request('OPTIONS', $uri, $headers);

        return $this->sendRequest($request);
    }

    /**
     * 创建请求上下文
     *
     * @param  RequestInterface  $request HTTP请求对象
     * @return resource 请求上下文资源
     */
    private function createContext(RequestInterface $request)
    {
        $options = [
            'http' => [
                'method' => $request->getMethod(),
                'header' => $this->buildHeaders($request->getHeaders()),
                'content' => $request->getBody()->getContents(),
            ],
        ];

        return stream_context_create($options);
    }

    /**
     * 构建请求头部信息
     *
     * @param  array  $headers 请求头部信息
     * @return string 构建完成的请求头部字符串
     */
    private function buildHeaders(array $headers): string
    {
        $headerLines = [];
        foreach ($headers as $key => $value) {
            $headerLines[] = sprintf('%s: %s', $key, implode(', ', $value));
        }

        return implode("\r\n", $headerLines);
    }
}
