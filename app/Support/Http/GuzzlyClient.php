<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Http;

use App\Support\Http\Handlers\StreamHandler;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise as P;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;

class GuzzlyClient extends Client
{
    public function __construct(array $config = [])
    {
        if (! isset($config['handler'])) {
            $config['handler'] = $this->getDefaultHandlerStack();
        }

        parent::__construct($config);
    }

    /**
     * @see \GuzzleHttp\HandlerStack::create
     * @see \GuzzleHttp\Utils::chooseHandler
     */
    private function getDefaultHandlerStack(): HandlerStack
    {
        $handlerStack = new HandlerStack(static function (RequestInterface $request, array $options): PromiseInterface {
            try {
                return new FulfilledPromise((new StreamHandler())($request, $options));
            } catch (\Throwable $e) {
                return P\Create::rejectionFor(
                    new RequestException('An error was encountered while creating the response', $request, null, $e)
                );
            }
        });
        $handlerStack->push(Middleware::httpErrors(), 'http_errors');
        $handlerStack->push(Middleware::redirect(), 'allow_redirects');
        $handlerStack->push(Middleware::cookies(), 'cookies');
        $handlerStack->push(Middleware::prepareBody(), 'prepare_body');

        return $handlerStack;
    }
}
