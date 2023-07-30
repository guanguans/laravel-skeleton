<?php

declare(strict_types=1);

namespace App\Support\Http;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise as P;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

class Client extends \GuzzleHttp\Client
{
    private ClientInterface $psrClient;

    public function __construct(array $config = [], ?ClientInterface $psrClient = null)
    {
        if (! isset($config['handler'])) {
            $config['handler'] = $this->getDefaultHandlerStack();
        }

        parent::__construct($config);
        $this->psrClient = $psrClient ?? new FgcPsrClient($this->getConfig());
    }

    /**
     * @see \GuzzleHttp\HandlerStack::create
     * @see \GuzzleHttp\Utils::chooseHandler
     */
    private function getDefaultHandlerStack(): HandlerStack
    {
        $handlerStack = new HandlerStack(function (RequestInterface $request, array $options): PromiseInterface {
            try {
                return new FulfilledPromise($this->psrClient->sendRequest($request));
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
