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

namespace App\Support;

use Nyholm\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class Curl implements ClientInterface
{
    public const int DEFAULT_CONNECTION_TIMEOUT = 0;
    private const array BODYLESS_HTTP_METHODS = ['HEAD', 'GET'];
    private const string HTTP_SPEC_CRLF = "\r\n";
    private const string HTTP_SPEC_SP = ' ';
    private \CurlHandle $curl;

    /**
     * @param list<mixed> $options
     */
    public function __construct(private array $options = []) {}

    /**
     * @param list<mixed> $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        curl_reset($this->getCurl());

        $headers = [];

        foreach ($request->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                $headers[] = \sprintf('%s: %s', $name, $value);
            }
        }

        $curlOptions = [
            \CURLOPT_HTTP_VERSION => $this->getCurlHttpVersion($request->getProtocolVersion()),
            \CURLOPT_CUSTOMREQUEST => $request->getMethod(),
            \CURLOPT_CONNECTTIMEOUT => 0,
            \CURLOPT_URL => (string) $request->getUri(),
            \CURLOPT_NOBODY => $request->getMethod() === 'HEAD',
            \CURLOPT_RETURNTRANSFER => true,
            \CURLOPT_HEADER => true,
            \CURLOPT_HTTPHEADER => $headers,
        ];

        if (!\in_array($request->getMethod(), self::BODYLESS_HTTP_METHODS, true)) {
            $curlOptions[\CURLOPT_POSTFIELDS] = (string) $request->getBody();
        }

        curl_setopt_array($this->getCurl(), array_replace($curlOptions, $this->options));
        $response = curl_exec($this->getCurl());

        if (false !== $response && curl_errno($this->getCurl()) === 0) {
            $parse = $this->parseResponse((string) $response);

            return new Response(
                $parse['status-code'],
                $parse['headers'],
                $parse['body'],
                $parse['http-version'],
                $parse['reason-phrase']
            );
        }

        throw new \RuntimeException(\sprintf(
            'Error sending with cURL (%d): %s',
            curl_errno($this->getCurl()),
            curl_error($this->getCurl())
        ));
    }

    /**
     * @throws \Throwable
     */
    private function getCurl(): \CurlHandle
    {
        if (empty($this->curl)) {
            $init = curl_init();

            throw_if(false === $init, \RuntimeException::class, 'I cannot execute curl initialization');

            $this->curl = $init;
        }

        return $this->curl;
    }

    /**
     * Return cURL constant for specified HTTP version.
     *
     * @throws \RuntimeException if unsupported version requested
     */
    private function getCurlHttpVersion(string $version): int
    {
        switch ($version) {
            case '1.0':
                return \CURL_HTTP_VERSION_1_0;
            case '1.1':
                return \CURL_HTTP_VERSION_1_1;
            case '2.0':
                if (\defined('CURL_HTTP_VERSION_2_0')) {
                    return \CURL_HTTP_VERSION_2_0;
                }

                throw new \RuntimeException('libcurl 7.33 needed for HTTP 2.0 support');
        }

        return \CURL_HTTP_VERSION_NONE;
    }

    /**
     * Parses the HTTP response from curl and
     * generates the start-line, headers and the body.
     *
     * @see https://datatracker.ietf.org/doc/html/rfc7230#section-3
     *
     * @return array{
     *      http-version: string, // HTTP version (e.g. if HTTP/1.1 http-version is "1.1")
     *      status-code: int, // The status code of the response (e.g. 200)
     *      reason-phrase: string, // The reason-phrase (e.g. OK)
     *      headers: array<mixed>, // The HTTP headers
     *      body: string, // The body content (can be empty)
     * }
     */
    private function parseResponse(string $response): array
    {
        $lines = explode(self::HTTP_SPEC_CRLF, $response);
        $output = [
            'http-version' => '',
            'status-code' => 200,
            'reason-phrase' => '',
            'headers' => [],
            'body' => '',
        ];

        foreach ($lines as $index => $line) {
            // status-line
            // @see https://datatracker.ietf.org/doc/html/rfc7230#section-3.1.2
            if (0 === $index) {
                $statusLine = explode(self::HTTP_SPEC_SP, $line, 3);
                $output['http-version'] = explode('/', $statusLine[0], 2)[1];
                $output['status-code'] = (int) $statusLine[1];
                $output['reason-phrase'] = $statusLine[2];

                continue;
            }

            // Empty line, end of headers
            if (empty($line)) {
                $output['body'] = $lines[$index + 1] ?? '';

                break;
            }

            // Extract header name and values
            [$name, $value] = explode(':', $line, 2);

            if (isset($output['headers'][$name])) {
                $output['headers'][$name][] = $value;
            } else {
                $output['headers'][$name] = [$value];
            }
        }

        return $output;
    }
}
