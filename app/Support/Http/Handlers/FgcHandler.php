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

namespace App\Support\Http\Handlers;

use App\Support\Http\Contracts\Handler;
use GuzzleHttp\Handler\HeaderProcessor;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class FgcHandler implements Handler
{
    /**
     * @see https://www.php.net/manual/zh/function.file-get-contents
     * @see https://www.php.net/manual/zh/function.stream-context-create.php
     * @see https://www.php.net/manual/zh/context.http.php
     * @see https://www.php.net/manual/zh/function.stream-context-set-params.php
     * @see https://www.php.net/manual/zh/function.stream-notification-callback.php
     *
     * @noinspection PhpExpressionResultUnusedInspection
     *
     * @param array{
     *     method: string,
     *     header: array|string,
     *     user_agent: string,
     *     content: string,
     *     proxy: string,
     *     request_fulluri: bool,
     *     follow_location: int,
     *     max_redirects: int,
     *     protocol_version: float|string,
     *     timeout: float,
     *     ignore_errors: bool,
     *     notification: null|callable(int, int, string, int, int, int): void,
     *     progress: null|callable(int, int): void,
     * } $options
     */
    public function __invoke(RequestInterface $request, array $options): ResponseInterface
    {
        if (!\ini_get('allow_url_fopen')) {
            throw new \RuntimeException('StreamPsrClient require `allow_url_fopen` to be enabled in php.ini');
        }

        $uri = (string) $request->getUri();
        $streamContext = $this->toStreamContext($request, $this->configureOptions($options));

        set_error_handler(static function (int $errno, string $errstr) use (&$error): void {
            $error = $errstr;
        });
        $responseBody = file_get_contents($uri, false, $streamContext);
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
            if (\STREAM_NOTIFY_PROGRESS === $notificationCode) {
                // https://www.php.net/manual/zh/function.stream-notification-callback.php#121236
                0 < $bytesTransferred and $bytesTransferred += 8192;
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

    private function configureOptions(array $options): array
    {
        $options += [
            'protocol_version' => '1.1',
            'ignore_errors' => true,
            'notification' => null,
            'progress' => null,
        ];

        if (isset($options['notification'], $options['progress'])) {
            throw new \InvalidArgumentException('You cannot use notification and progress at the same time.');
        }

        if (\is_callable($options['progress'])) {
            $options['notification'] = self::toProgressNotification($options['progress']);
        }

        return $options;
    }

    private function toStreamContext(RequestInterface $request, array $options = [])
    {
        $options = [
            'method' => $request->getMethod(),
            'header' => self::toIndexHeaders($request),
            'content' => (string) $request->getBody(),
            'protocol_version' => $request->getProtocolVersion(),
        ] + $options;

        return stream_context_create(['http' => $options], ['notification' => $options['notification']]);
    }
}
