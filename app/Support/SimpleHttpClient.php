<?php

declare(strict_types=1);

namespace App\Support;

use Nyholm\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class SimpleHttpClient implements ClientInterface
{
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $streamContextOptions = [
            'protocol_version' => $request->getProtocolVersion(),
            'method' => $request->getMethod(),
            'header' => $this->toLineHeaders($request),
            'timeout' => 30,
            'ignore_errors' => true,
            'follow_location' => 0,
            'max_redirects' => 100,
            'content' => (string) $request->getBody(),
        ];

        $streamContext = stream_context_create([
            'http' => $streamContextOptions,
            'https' => $streamContextOptions,
            'ssl' => [
                'verify_peer' => false,
            ],
        ]);

        /*
         * file_get_contents() will issue PHP warnings, these should be converted to exceptions thus
         * set_error_handler() / restore_error_handler() is used
         */
        set_error_handler(static function ($errno, $errstr, $errfile, $errline, $errcontext) use (&$errors): void {
            $errors[] = $errstr;
        });
        $responseBody = file_get_contents((string) $request->getUri(), false, $streamContext);
        restore_error_handler();

        if (false === $responseBody && $errors) {
            throw new \RuntimeException(implode('; ', $errors));
        }

        [$status, $version, $reason] = $this->getHttp();

        return new Response(
            $status,
            $this->toAssocHeaders(\array_slice($http_response_header, 1)),
            $responseBody,
            $version,
            $reason
        );
    }

    private function getHttp()
    {
        /** @var array $http */
        $http = explode(' ', $http_response_header[0]);

        return [
            'status' => (int) $http[1],
            'protocol' => explode('/', $http[0], 2)[1],
            'reason' => implode(' ', \array_slice($http, 2)),
        ];

    }

    private function toLineHeaders(RequestInterface $request): string
    {
        $headers = array_reduce(
            array_keys($request->getHeaders()),
            static function (array $headers, string $name) use ($request): array {
                $values = 'content-length' === strtolower($name)
                    ? [\strlen((string) $request->getBody())]
                    : $request->getHeader($name);

                return array_reduce($values, static function (array $headers, string $value) use ($name): array {
                    $headers[] = "{$name}: {$value}";

                    return $headers;
                }, $headers);
            },
            []
        );

        return implode("\r\n", $headers);
    }

    private function toAssocHeaders(array $lineHeaders)
    {
        return array_column(
            array_map(
                static fn ($lineHeader) => preg_split('#:\s+#', $lineHeader, 2),
                $lineHeaders
            ),
            1,
            0
        );
    }
}
