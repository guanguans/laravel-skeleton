<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
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
     * @param  \Closure(object): void  $next
     */
    public function handle(object $job, \Closure $next): void
    {
        $next($job);
    }
}
