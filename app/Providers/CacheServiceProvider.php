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

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Traits\Conditionable;

class CacheServiceProvider extends ServiceProvider
{
    use Conditionable {
        Conditionable::when as whenever;
    }

    public function boot(): void
    {
        // Route::middleware(['throttle:upload']);
        RateLimiter::for(
            'upload',
            static fn (Request $request) => $request->user()->vipCustomer()
                ? Limit::none()
                : Limit::perMinute(100)->by($request->ip())
        );

        RateLimiter::for(
            'api',
            static fn (Request $request) => Limit::perMinute(60)->by($request->user()?->id ?: $request->ip())
        );

        RateLimiter::for(
            'login',
            static fn (Request $request): array => [
                Limit::perMinute(500),
                Limit::perMinute(5)->by($request->ip()),
                Limit::perMinute(5)->by($request->input('email')),
            ]
        );
    }
}
