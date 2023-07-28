<?php

declare(strict_types=1);

namespace App\Support;

use Nyholm\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class SimpleHttpClient implements ClientInterface
{
    public function __construct(private array $options = [])
    {
        $this->setOptions($options);
    }

    public function setOptions(array $options): self
    {
        $this->options = $options + [
            'method' => 'GET',
            'header' => [],
            'user_agent' => \ini_get('user_agent'),
            'content' => '',
            'proxy' => '',
            'request_fulluri' => false,
            'follow_location' => 1,
            'max_redirects' => 20,
            'protocol_version' => '1.1',
            'timeout' => \ini_get('default_socket_timeout'),
            'ignore_errors' => true,
        ];

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        set_error_handler(static function (int $errno, string $errstr, ?string $errfile = null, ?int $errline = null) use (&$errors): void {
            // Warning: file_get_contents(/api/any): Failed to open stream: No such file or directory in /Users/yaozm/Documents/wwwroot/laravel-skeleton/app/Support/SimpleHttpClient.php on line 25
            $errors[] = sprintf('%s: %s in %s on line %s', $errno, $errstr, $errfile, $errline);
        });
        $responseBody = file_get_contents((string) $request->getUri(), false, $this->toStreamContext($request));
        restore_error_handler();

        if (false === $responseBody && $errors) {
            throw new \RuntimeException(implode(PHP_EOL, $errors));
        }

        $http = $this->toHttp($http_response_header);

        return new Response(
            $http['status'],
            $this->toAssocHeaders($http_response_header),
            $responseBody,
            $http['protocol'],
            $http['reason']
        );
    }

    private function toHttp(array $http_response_header): array
    {
        /** @var array $http */
        $http = explode(' ', $http_response_header[0]);

        return [
            'status' => (int) $http[1],
            'protocol' => explode('/', $http[0], 2)[1],
            'reason' => implode(' ', \array_slice($http, 2)),
        ];
    }

    private function toIndexHeaders(RequestInterface $request): array
    {
        return array_reduce(
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
    }

    private function toAssocHeaders(array $http_response_header): array
    {
        $sterilizedLineHeaders = \array_slice($http_response_header, 1);

        return array_column(
            array_map(
                static fn ($lineHeader) => preg_split('#:\s+#', $lineHeader, 2),
                $sterilizedLineHeaders
            ),
            1,
            0
        );
    }

    private function toStreamContext(RequestInterface $request)
    {
        $options = [
            'method' => $request->getMethod(),
            'header' => $this->toIndexHeaders($request),
            'content' => (string) $request->getBody(),
            'protocol_version' => $request->getProtocolVersion(),
        ] + $this->options;

        return stream_context_create(['http' => $options]);
    }
}
