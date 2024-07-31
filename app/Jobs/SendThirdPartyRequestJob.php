<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

/**
 * @see https://ammar-aldwayma.me/blog/dealing-with-an-unstable-services-in-laravel
 * @see https://www.harrisrafto.eu/enhancing-your-laravel-job-handling-with-middleware-managing-httpclientexception
 */
class SendThirdPartyRequestJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tires = 3;

    public function handle(): void
    {
        Http::acceptJson()->timeout(10)->get('https://...');

        // Use the response to run the business logic ...
    }

    public function middleware(): array
    {
        return [
            // Circuit Breaker Pattern - 断路器模式中间件
            (new ThrottlesExceptions(maxAttempts: 3, decaySeconds: 300))
                ->by(self::class)
                ->backoff(1)
                ->when(static fn (\Throwable $e): bool => $e instanceof HttpClientException),
        ];
    }
}
