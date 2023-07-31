<?php

declare(strict_types=1);

namespace App\Support\Http;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class StreamPsrClient implements ClientInterface
{
    private StreamHandler $streamHandler;

    /**
     * @see \GuzzleHttp\RequestOptions for a list of available request options.
     */
    public function __construct(private array $options = [], ?StreamHandler $streamHandler = null)
    {
        $this->streamHandler = $streamHandler ?? new StreamHandler();
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return ($this->streamHandler)($request, $this->options);
    }
}
