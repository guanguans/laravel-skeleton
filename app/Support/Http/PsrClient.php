<?php

/** @noinspection PhpInternalEntityUsedInspection */

declare(strict_types=1);

namespace App\Support\Http;

use GuzzleHttp\Handler\HeaderProcessor;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class PsrClient implements ClientInterface
{
    public function __construct(private array $options = [])
    {
        $this->setOptions($options);
    }

    /**
     * @noinspection PhpExpressionResultUnusedInspection
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $uri = (string) $request->getUri();
        $streamContext = $this->toStreamContext($request);

        set_error_handler(static function (
            int $errno,
            string $errstr,
            ?string $errfile = null,
            ?int $errline = null
        ) use (&$errors): void {
            // Warning: file_get_contents(/api/any): Failed to open stream: No such file or directory in /...Client.php on line 25
            $error = sprintf('Errno %s: %s', $errno, $errstr);
            $errfile and $error .= " in {$errfile}";
            $errline and $error .= " on line {$errline}";
            $errors[] = $error;
        });
        $responseBody = file_get_contents($uri, false, $streamContext);
        restore_error_handler();

        if (false === $responseBody && $errors) {
            throw new \RuntimeException(implode(PHP_EOL, $errors));
        }

        [$version, $status, $reason, $headers] = HeaderProcessor::parseHeaders($http_response_header);

        return new Response($status, $headers, $responseBody, $version, $reason);
    }

    private function setOptions(array $options): void
    {
        $defaults = [
            // 'method' => 'GET',
            // 'header' => [],
            // 'user_agent' => \ini_get('user_agent'),
            // 'content' => '',
            // 'proxy' => '',
            // 'request_fulluri' => false,
            // 'follow_location' => 1,
            // 'max_redirects' => 20,
            'protocol_version' => '1.1',
            // 'timeout' => \ini_get('default_socket_timeout'),
            'ignore_errors' => true,

            // https://www.php.net/manual/zh/function.stream-notification-callback.php
            'notification' => null,
        ];

        $this->options = $options + $defaults;
    }

    private function toStreamContext(RequestInterface $request)
    {
        $options = [
            'method' => $request->getMethod(),
            'header' => $this->toIndexHeaders($request),
            'content' => (string) $request->getBody(),
            'protocol_version' => $request->getProtocolVersion(),
        ] + $this->options;

        return stream_context_create(['http' => $options], ['notification' => $options['notification']]);
    }

    private function toIndexHeaders(RequestInterface $request): array
    {
        return array_reduce(
            array_keys($request->getHeaders()),
            static function (array $headers, string $name) use ($request): array {
                $values = 'content-length' === strtolower($name)
                    ? [\strlen((string) $request->getBody())]
                    : $request->getHeader($name);

                foreach ($values as $value) {
                    $headers[] = "{$name}: {$value}";
                }

                return $headers;
            },
            []
        );
    }
}
