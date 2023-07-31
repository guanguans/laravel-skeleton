<?php

declare(strict_types=1);

namespace App\Support\Http;

use App\Support\Http\Contracts\Handler;
use App\Support\Http\Handlers\StreamHandler;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class PsrClient implements ClientInterface
{
    /** @var null|callable|Handler */
    private $handler;

    /**
     * @see \GuzzleHttp\RequestOptions for a list of available request options.
     */
    public function __construct(private array $options = [], null|Handler|callable $handler = null)
    {
        $this->handler = $handler ?? new StreamHandler();
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return ($this->handler)($request, $this->options);
    }
}
