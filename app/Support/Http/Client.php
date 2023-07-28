<?php

declare(strict_types=1);

namespace App\Support\Http;

use GuzzleHttp\Exception\InvalidArgumentException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Utils;
use Nyholm\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

class Client implements ClientInterface
{
    use ClientTrait;

    public function __construct(private array $config = [])
    {
        if (! isset($config['handler'])) {
            $config['handler'] = HandlerStack::create();
        } elseif (! \is_callable($config['handler'])) {
            throw new InvalidArgumentException('handler must be a callable');
        }

        // Convert the base_uri to a UriInterface
        if (isset($config['base_uri'])) {
            $config['base_uri'] = \GuzzleHttp\Psr7\Utils::uriFor($config['base_uri']);
        }

        $this->configureDefaults($config);
    }

    public function request(string $method, $uri, array $options = []): ResponseInterface
    {
        $options = $this->prepareDefaults($options);
        // Remove request modifying parameter because it can be done up-front.
        $headers = $options['headers'] ?? [];
        $body = $options['body'] ?? null;
        $version = $options['version'] ?? '1.1';
        // Merge the URI into the base URI.
        $uri = $this->buildUri(Psr7\Utils::uriFor($uri), $options);
        if (\is_array($body)) {
            throw $this->invalidBody();
        }
        $request = new Psr7\Request($method, $uri, $headers, $body, $version);
        // Remove the option so that they are not doubly-applied.
        unset($options['headers'], $options['body'], $options['version']);

        return $this->transfer($request, $options);
    }

    /**
     * Asynchronously send an HTTP request.
     *
     * @param  array  $options Request options to apply to the given
     *                       request and to the transfer. See \GuzzleHttp\RequestOptions.
     */
    public function sendAsync(RequestInterface $request, array $options = []): PromiseInterface
    {
        // Merge the base URI into the request URI if needed.
        $options = $this->prepareDefaults($options);

        return $this->transfer(
            $request->withUri($this->buildUri($request->getUri(), $options), $request->hasHeader('Host')),
            $options
        );
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

    /**
     * Merges default options into the array.
     *
     * @param  array  $options Options to modify by reference
     */
    private function prepareDefaults(array $options): array
    {
        $defaults = $this->config;

        if (! empty($defaults['headers'])) {
            // Default headers are only added if they are not present.
            $defaults['_conditional'] = $defaults['headers'];
            unset($defaults['headers']);
        }

        // Special handling for headers is required as they are added as
        // conditional headers and as headers passed to a request ctor.
        if (\array_key_exists('headers', $options)) {
            // Allows default headers to be unset.
            if (null === $options['headers']) {
                $defaults['_conditional'] = [];
                unset($options['headers']);
            } elseif (! \is_array($options['headers'])) {
                throw new InvalidArgumentException('headers must be an array');
            }
        }

        // Shallow merge defaults underneath options.
        $result = $options + $defaults;

        // Remove null values.
        foreach ($result as $k => $v) {
            if (null === $v) {
                unset($result[$k]);
            }
        }

        return $result;
    }

    private function configureDefaults(array $config): void
    {
        $defaults = [
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

            'base_uri' => '',
            'body' => [],
            'form_params' => [],
            'json' => [],
            'multipart' => [],
            'query' => [],
            'handler' => null,
        ];

        // Use the standard Linux HTTP_PROXY and HTTPS_PROXY if set.

        // We can only trust the HTTP_PROXY environment variable in a CLI
        // process due to the fact that PHP has no reliable mechanism to
        // get environment variables that start with "HTTP_".
        if (\PHP_SAPI === 'cli' && ($proxy = Utils::getenv('HTTP_PROXY'))) {
            $defaults['proxy'] = $proxy;
        }

        if ($proxy = Utils::getenv('HTTPS_PROXY')) {
            $defaults['proxy'] = $proxy;
        }

        if (Utils::getenv('NO_PROXY')) {
            $defaults['proxy'] = '';
        }

        $this->config = $config + $defaults;
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
        ] + $this->config;

        return stream_context_create(['http' => $options]);
    }
}
