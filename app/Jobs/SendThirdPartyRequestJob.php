<?php

/** @noinspection PhpUnusedAliasInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Queue\Attributes\WithoutRelations;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\Skip;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
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

    /** Delete the job if its models no longer exist. */
    public bool $deleteWhenMissingModels = true;

    public function __construct(
        #[WithoutRelations]
        private readonly User $user
    ) {}

    /**
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function handle(): void
    {
        Http::acceptJson()->timeout(10)->get('https://...');

        // Use the response to run the business logic ...
    }

    public function middleware(): array
    {
        return [
            // Skip::when(true),
            // Skip::unless(false),
            new SkipIfBatchCancelled,
            // Circuit Breaker Pattern - 断路器模式中间件
            (new ThrottlesExceptions(maxAttempts: 3, decaySeconds: 300))
                ->by(self::class)
                ->backoff(1)
                ->when(static fn (\Throwable $e): bool => $e instanceof HttpClientException),
        ];
    }
}
