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

use Ackintosh\Ganesha;
use Ackintosh\Ganesha\GuzzleMiddleware;
use Ackintosh\Ganesha\GuzzleMiddleware\FailureDetectorInterface;
use Ackintosh\Ganesha\GuzzleMiddleware\ServiceNameExtractorInterface;
use Ackintosh\Ganesha\Storage\Adapter\Redis;
use Ackintosh\Ganesha\Strategy\Rate\Builder;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Tappable;

class CircuitBreakerMiddleware
{
    use Conditionable;
    use Tappable;
    private Ganesha $ganesha;

    public function __construct(
        array $configuration = [],
        private ?ServiceNameExtractorInterface $serviceNameExtractor = null,
        private ?FailureDetectorInterface $failureDetector = null
    ) {
        // 当每 600 秒触发 10 次异常，就会有 90% 的概率的打开断路器，且每 30 秒关闭一次断路器。
        $configuration += [
            'timeWindow' => 600,
            'minimumRequests' => 10,
            'failureRateThreshold' => 90,
            'intervalToHalfOpen' => 30,
            'adapter' => new Redis(\Illuminate\Support\Facades\Redis::connection()->client()),
            // 'storageKeys' => \Ackintosh\Ganesha\Storage\StorageKeysInterface::class,
        ];

        $this->ganesha = collect($configuration)
            ->reduce(
                static fn (
                    Builder $builder,
                    mixed $parameter,
                    string $method
                ): Builder => $builder->{$method}($parameter),
                Ganesha\Builder::withRateStrategy()
            )
            ?->build();
    }

    public function __invoke(callable $handler): \Closure
    {
        // @throws \Ackintosh\Ganesha\Exception\RejectedException
        return (new GuzzleMiddleware(
            $this->ganesha,
            $this->serviceNameExtractor,
            $this->failureDetector
        ))($handler);
    }

    public function getGanesha(): Ganesha
    {
        return $this->ganesha;
    }

    public function setServiceNameExtractor(?ServiceNameExtractorInterface $serviceNameExtractor): self
    {
        $this->serviceNameExtractor = $serviceNameExtractor;

        return $this;
    }

    public function setFailureDetector(?FailureDetectorInterface $failureDetector): self
    {
        $this->failureDetector = $failureDetector;

        return $this;
    }
}
