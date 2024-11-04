<?php

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
