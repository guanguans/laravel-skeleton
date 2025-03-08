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

namespace App\Support;

use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Sleep;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use TencentAds\V3\TencentAds;

/**
 * @method int findByName(string $name)
 */
class TencentAdsRateLimiterMiddleware
{
    /** @var array<string, int> */
    private static array $remains = [];
    private readonly int $deferMicroseconds;

    public function __construct(int $deferMilliseconds = 3000)
    {
        $this->deferMicroseconds = $deferMilliseconds * 1000;
    }

    public function __invoke(callable $handler): \Closure
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $fingerprint = rtrim("{$request->getMethod()}|{$request->getUri()->getPath()}", '/');

            if (isset(self::$remains[$fingerprint]) && 0 >= self::$remains[$fingerprint]) {
                Sleep::usleep($this->deferMicroseconds);
            }

            return $handler($request, $options)->then(
                static function (ResponseInterface $response) use ($fingerprint): ResponseInterface {
                    if ($response->hasHeader($name = 'X-RateLimit-Remaining')) {
                        [, $remain] = explode(',', $response->getHeaderLine($name), 2);
                        self::$remains[$fingerprint] = (int) $remain;
                        // dump(self::$remains);
                    }

                    return $response;
                }
            );
        };
    }

    public static function apply(TencentAds $tencentAds, int $deferMilliseconds = 3000): TencentAds
    {
        $httpOptions = $tencentAds->getHttpOptions();
        $handlerStack = $httpOptions['handler'] ?? HandlerStack::create();
        $handlerStack->setHandler(new CurlHandler);

        try {
            (fn (string $name) => $this->findByName($name))->call($handlerStack, self::name());
        } catch (\InvalidArgumentException) {
            $handlerStack->push(new self($deferMilliseconds), self::name());
            $httpOptions = ['handler' => $handlerStack] + $httpOptions;
        }

        return $tencentAds->setHttpOptions($httpOptions);
    }

    public static function name(): string
    {
        return self::class;
    }
}
