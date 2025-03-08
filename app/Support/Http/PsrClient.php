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
    public function __construct(
        private array $options = [],
        null|callable|Handler $handler = null
    ) {
        $this->handler = $handler ?? new StreamHandler;
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->send($request);
    }

    public function send(RequestInterface $request, array $options = []): ResponseInterface
    {
        return ($this->handler)($request, $options + $this->options);
    }
}
