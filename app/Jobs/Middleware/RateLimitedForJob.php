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

namespace App\Jobs\Middleware;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Redis;

/**
 * @see https://www.harrisrafto.eu/throttling-jobs-in-laravel-to-prevent-api-flooding
 */
final class RateLimitedForJob
{
    /**
     * @noinspection PhpDocSignatureInspection
     *
     * @param \Illuminate\Queue\CallQueuedClosure $job
     *
     * @throws \Illuminate\Contracts\Redis\LimiterTimeoutException
     */
    public function handle(ShouldQueue $job, \Closure $next): void
    {
        Redis::throttle(self::class)
            ->block(2) // 指定任务获得锁最多等待 2 秒。
            ->allow(10)->every(2) // 每 2 秒最多允许 10 次操作。
            ->then(
                static function () use ($next, $job): void {
                    $next($job); // 如果获得锁定，则作业继续进行。
                },
                static function () use ($job): void {
                    $job->release(30); // 如果在阻塞时间内未获得锁定，则作业将释放回队列，以便在 30 秒后再次尝试。
                }
            );
    }
}
