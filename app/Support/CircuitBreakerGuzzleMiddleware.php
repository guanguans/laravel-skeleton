<?php

declare(strict_types=1);

namespace App\Support;

use Ackintosh\Ganesha\GuzzleMiddleware;
use Ackintosh\Ganesha\Storage\Adapter\Redis;
use Ackintosh\Ganesha\Strategy\Rate\Builder;

class CircuitBreakerGuzzleMiddleware
{
    private readonly GuzzleMiddleware $guzzleMiddleware;

    public function __construct(array $configuration = [])
    {
        // 当每 600 秒触发 10 次异常，就会有 90% 的概率的打开断路器，且每 30 秒关闭一次断路器。
        $configuration += [
            'timeWindow' => 600,
            'minimumRequests' => 10,
            'failureRateThreshold' => 90,
            'intervalToHalfOpen' => 30,
            'adapter' => new Redis(\Illuminate\Support\Facades\Redis::connection()->client()),
            // 'storageKeys' => \Ackintosh\Ganesha\Storage\StorageKeysInterface::class,
        ];

        $ganesha = collect($configuration)
            ->reduce(
                static fn (
                    Builder $builder,
                    mixed $parameter,
                    string $method
                ): Builder => $builder->{$method}($parameter),
                \Ackintosh\Ganesha\Builder::withRateStrategy()
            )
            ?->build();

        $this->guzzleMiddleware = new GuzzleMiddleware($ganesha);
    }

    public function __invoke(callable $handler): \Closure
    {
        /** @throws \Ackintosh\Ganesha\Exception\RejectedException */
        return ($this->guzzleMiddleware)($handler);
    }
}
