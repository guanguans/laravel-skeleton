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

/**
 * @see https://www.daveyshafik.com/archives/70916-laravel-pipelines-and-composable-job-middleware.html
 */
class EnsureTokenIsValid
{
    /**
     * Process the queued job.
     *
     * @param \Closure(object): void $next
     */
    public function handle(object $job, \Closure $next): void
    {
        $next($job);
    }
}
