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
        if (!isset($config['handler'])) {
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
                return new FulfilledPromise((new StreamHandler)($request, $options));
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
