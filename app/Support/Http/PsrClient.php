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
    /**
     * @param array{
     *     method: string,
     *     header: array|string,
     *     user_agent: string,
     *     content: string,
     *     proxy: string,
     *     request_fulluri: bool,
     *     follow_location: int,
     *     max_redirects: int,
     *     protocol_version: float,
     *     timeout: float,
     *     ignore_errors: bool,
     *     notification: null|callable(int, int, string, int, int, int): void,
     *     progress: null|callable(int, int): void,
     * } $config
     *
     * @see https://www.php.net/manual/zh/function.file-get-contents
     * @see https://www.php.net/manual/zh/function.stream-context-create.php
     * @see https://www.php.net/manual/zh/context.http.php
     * @see https://www.php.net/manual/zh/function.stream-context-set-params.php
     * @see https://www.php.net/manual/zh/function.stream-notification-callback.php
     */
    public function __construct(private array $config = [])
    {
        $this->configureDefaults($config);
    }

    /**
     * @noinspection PhpExpressionResultUnusedInspection
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        set_error_handler(static function (
            int $errno,
            string $errstr,
            ?string $errfile = null,
            ?int $errline = null
        ) use (&$error): void {
            // Warning: file_get_contents(/api/any): Failed to open stream: No such file or directory in /...Client.php on line 25
            // $error = "Errno {$errno}: {$errstr}";
            // $errfile and $error .= " in {$errfile}";
            // $errline and $error .= " on line {$errline}";
            $error = $errstr;
        });
        $responseBody = file_get_contents((string) $request->getUri(), false, $this->toStreamContext($request));
        restore_error_handler();

        if (false === $responseBody && $error) {
            throw new \RuntimeException($error);
        }

        [$version, $status, $reason, $headers] = HeaderProcessor::parseHeaders($http_response_header);

        return new Response($status, $headers, $responseBody, $version, $reason);
    }

    private static function toProgressNotification(callable $process): \Closure
    {
        return static function (
            int $notificationCode,
            int $severity,
            string $message,
            int $messageCode,
            int $bytesTransferred,
            int $bytesMax
        ) use ($process): void {
            if (STREAM_NOTIFY_PROGRESS === $notificationCode) {
                // https://www.php.net/manual/zh/function.stream-notification-callback.php#121236
                $bytesTransferred > 0 and $bytesTransferred += 8192;
                $process($bytesMax, $bytesTransferred);
            }
        };
    }

    private static function toIndexHeaders(RequestInterface $request): array
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

    private function configureDefaults(array $config): void
    {
        $defaults = [
            'protocol_version' => '1.1',
            'ignore_errors' => true,
            'notification' => null,
            'progress' => null,
        ];

        $config += $defaults;

        if (isset($config['notification'], $config['progress'])) {
            throw new \InvalidArgumentException('You cannot use notification and progress at the same time.');
        }

        if (\is_callable($config['progress'])) {
            $config['notification'] = self::toProgressNotification($config['progress']);
        }

        $this->config = $config;
    }

    private function toStreamContext(RequestInterface $request)
    {
        $config = [
            'method' => $request->getMethod(),
            'header' => self::toIndexHeaders($request),
            'content' => (string) $request->getBody(),
            'protocol_version' => $request->getProtocolVersion(),
        ] + $this->config;

        return stream_context_create(['http' => $config], ['notification' => $config['notification']]);
    }
}
