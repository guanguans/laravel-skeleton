<?php

declare(strict_types=1);

namespace App\Support\Http;

use App\Support\Http\Concerns\ConcreteHttpRequestMethods;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\UriResolver;
use GuzzleHttp\Utils;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

class SimpleClient implements ClientInterface
{
    use ConcreteHttpRequestMethods;

    public function __construct(private array $config = [], private ?ClientInterface $psrClient = null)
    {
        if (! isset($config['handler'])) {
            $config['handler'] = new HandlerStack($this->handler());
        } elseif (! \is_callable($config['handler'])) {
            throw new \InvalidArgumentException('handler must be a callable');
        }

        // Convert the base_uri to a UriInterface
        if (isset($config['base_uri'])) {
            $config['base_uri'] = \GuzzleHttp\Psr7\Utils::uriFor($config['base_uri']);
        }

        $this->configureDefaults($config);
        $this->psrClient ??= new PsrClient($this->config);
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->send($request);
    }

    public function send(RequestInterface $request, array $options = []): ResponseInterface
    {
        // Merge the base URI into the request URI if needed.
        $options = $this->prepareDefaults($options);

        return $this->transfer(
            $request->withUri($this->buildUri($request->getUri(), $options), $request->hasHeader('Host')),
            $options
        );
    }

    /**
     * @param  string|UriInterface  $uri
     */
    public function request(string $method, $uri = '', array $options = []): ResponseInterface
    {
        $options = $this->prepareDefaults($options);
        // Remove request modifying parameter because it can be done up-front.
        $headers = $options['headers'] ?? [];
        $body = $options['body'] ?? null;
        $version = $options['protocol_version'] ?? '1.1';
        // Merge the URI into the base URI.
        $uri = $this->buildUri(\GuzzleHttp\Psr7\Utils::uriFor($uri), $options);
        if (\is_array($body)) {
            throw $this->invalidBody();
        }
        $request = new Request($method, $uri, $headers, $body, $version);
        // Remove the option so that they are not doubly-applied.
        unset($options['headers'], $options['body'], $options['protocol_version']);

        return $this->transfer($request, $options);
    }

    public function getConfig(?string $option = null): mixed
    {
        return null === $option
            ? $this->config
            : ($this->config[$option] ?? null);
    }

    private function buildUri(UriInterface $uri, array $config): UriInterface
    {
        if (isset($config['base_uri'])) {
            $uri = UriResolver::resolve(\GuzzleHttp\Psr7\Utils::uriFor($config['base_uri']), $uri);
        }

        if (isset($config['idn_conversion']) && (false !== $config['idn_conversion'])) {
            $idnOptions = (true === $config['idn_conversion']) ? IDNA_DEFAULT : $config['idn_conversion'];
            $uri = Utils::idnUriConvert($uri, $idnOptions);
        }

        return '' === $uri->getScheme() && '' !== $uri->getHost() ? $uri->withScheme('http') : $uri;
    }

    private function configureDefaults(array $config): void
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

            // 'auth' => [],
            // 'base_uri' => '',
            // 'body' => '',
            'decode_content' => true,
            // 'form_params' => [],
            // 'handler' => null,
            'idn_conversion' => false,
            // 'json' => [],
            // 'multipart' => [],
            // 'query' => [],
            // 'sink' => '',
            // 'cookies' => false,
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

        // Add the default user-agent header.
        if (! isset($this->config['headers'])) {
            $this->config['headers'] = ['User-Agent' => Utils::defaultUserAgent()];
        } else {
            // Add the User-Agent header if one was not already set.
            foreach (array_keys($this->config['headers']) as $name) {
                if ('user-agent' === strtolower($name)) {
                    return;
                }
            }
            $this->config['headers']['User-Agent'] = Utils::defaultUserAgent();
        }
    }

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
                throw new \InvalidArgumentException('headers must be an array');
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

    private function transfer(RequestInterface $request, array $options): ResponseInterface
    {
        $request = $this->applyOptions($request, $options);

        /** @var HandlerStack $handler */
        $handler = $options['handler'];

        return $handler($request, $options);
    }

    private function applyOptions(RequestInterface $request, array &$options): RequestInterface
    {
        $modify = [
            'set_headers' => [],
        ];

        if (isset($options['headers'])) {
            if (array_keys($options['headers']) === range(0, \count($options['headers']) - 1)) {
                throw new \InvalidArgumentException('The headers array must have header name as keys.');
            }
            $modify['set_headers'] = $options['headers'];
            unset($options['headers']);
        }

        if (isset($options['form_params'])) {
            if (isset($options['multipart'])) {
                throw new \InvalidArgumentException('You cannot use '
                    .'form_params and multipart at the same time. Use the '
                    .'form_params option if you want to send application/'
                    .'x-www-form-urlencoded requests, and the multipart '
                    .'option to send multipart/form-data requests.');
            }
            $options['body'] = http_build_query($options['form_params'], '', '&');
            unset($options['form_params']);
            // Ensure that we don't have the header in different case and set the new value.
            $options['_conditional'] = \GuzzleHttp\Psr7\Utils::caselessRemove(['Content-Type'], $options['_conditional']);
            $options['_conditional']['Content-Type'] = 'application/x-www-form-urlencoded';
        }

        if (isset($options['multipart'])) {
            $options['body'] = new MultipartStream($options['multipart']);
            unset($options['multipart']);
        }

        if (isset($options['json'])) {
            $options['body'] = Utils::jsonEncode($options['json']);
            unset($options['json']);
            // Ensure that we don't have the header in different case and set the new value.
            $options['_conditional'] = \GuzzleHttp\Psr7\Utils::caselessRemove(['Content-Type'], $options['_conditional']);
            $options['_conditional']['Content-Type'] = 'application/json';
        }

        if (! empty($options['decode_content'])
            && true !== $options['decode_content']
        ) {
            // Ensure that we don't have the header in different case and set the new value.
            $options['_conditional'] = \GuzzleHttp\Psr7\Utils::caselessRemove(['Accept-Encoding'], $options['_conditional']);
            $modify['set_headers']['Accept-Encoding'] = $options['decode_content'];
        }

        if (isset($options['body'])) {
            if (\is_array($options['body'])) {
                throw $this->invalidBody();
            }
            $modify['body'] = \GuzzleHttp\Psr7\Utils::streamFor($options['body']);
            unset($options['body']);
        }

        if (! empty($options['auth']) && \is_array($options['auth'])) {
            $value = $options['auth'];
            $type = isset($value[2]) ? strtolower($value[2]) : 'basic';

            switch ($type) {
                case 'basic':
                    // Ensure that we don't have the header in different case and set the new value.
                    $modify['set_headers'] = \GuzzleHttp\Psr7\Utils::caselessRemove(['Authorization'], $modify['set_headers']);
                    $modify['set_headers']['Authorization'] = 'Basic '
                        .base64_encode("$value[0]:$value[1]");

                    break;

                default:
                    throw new \InvalidArgumentException('Invalid or unsupported auth type specified: '.$type);
            }
        }

        if (isset($options['query'])) {
            $value = $options['query'];
            if (\is_array($value)) {
                $value = http_build_query($value, '', '&', PHP_QUERY_RFC3986);
            }
            if (! \is_string($value)) {
                throw new \InvalidArgumentException('query must be a string or array');
            }
            $modify['query'] = $value;
            unset($options['query']);
        }

        // Ensure that sink is not an invalid value.
        if (isset($options['sink'])) {
            // TODO: Add more sink validation?
            if (\is_bool($options['sink'])) {
                throw new \InvalidArgumentException('sink must not be a boolean');
            }
        }

        if (isset($options['protocol_version'])) {
            $modify['protocol_version'] = $options['protocol_version'];
        }

        $request = \GuzzleHttp\Psr7\Utils::modifyRequest($request, $modify);
        if ($request->getBody() instanceof MultipartStream) {
            // Use a multipart/form-data POST if a Content-Type is not set.
            // Ensure that we don't have the header in different case and set the new value.
            $options['_conditional'] = \GuzzleHttp\Psr7\Utils::caselessRemove(['Content-Type'], $options['_conditional']);
            $options['_conditional']['Content-Type'] = 'multipart/form-data; boundary='
                .$request->getBody()->getBoundary();
        }

        // Merge in conditional headers if they are not present.
        if (isset($options['_conditional'])) {
            // Build up the changes so it's in a single clone of the message.
            $modify = [];
            foreach ($options['_conditional'] as $k => $v) {
                if (! $request->hasHeader($k)) {
                    $modify['set_headers'][$k] = $v;
                }
            }
            $request = \GuzzleHttp\Psr7\Utils::modifyRequest($request, $modify);
            // Don't pass this internal value along to middleware/handlers.
            unset($options['_conditional']);
        }

        return $request;
    }

    private function invalidBody(): \InvalidArgumentException
    {
        return new \InvalidArgumentException('Passing in the "body" request '
            .'option as an array to send a request is not supported. '
            .'Please use the "form_params" request option to send a '
            .'application/x-www-form-urlencoded request, or the "multipart" '
            .'request option to send a multipart/form-data request.');
    }

    private function handler(): callable
    {
        return fn (RequestInterface $request, array $options): ResponseInterface => $this->psrClient->sendRequest($request);
    }
}
