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

use App\Models\User;
use App\Support\Contracts\ShouldRegisterContract;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider implements ShouldRegisterContract
{
    protected array $routeModels = [
        'user' => User::class,
    ];

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    public function boot(): void
    {
        $this->configureRateLimiting();
        Route::pattern('id', '[0-9]+');
        $this->bindRouteModels();
    }

    public function shouldRegister(): bool
    {
        return true;
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for(
            'api',
            static fn (Request $request) => Limit::perMinute(60)->by($request->user()?->id ?: $request->ip())
        );

        RateLimiter::for('login', static fn (Request $request): array => [
            Limit::perMinute(500),
            Limit::perMinute(5)->by($request->ip()),
            Limit::perMinute(5)->by($request->input('email')),
        ]);
    }

    protected function bindRouteModels(): void
    {
        Route::bind('user', static fn ($value) => User::query()->where('id', $value)->firstOrFail());

        foreach ($this->routeModels as $name => $model) {
            /** @noinspection UselessIsComparisonInspection */
            if (\is_int($name)) {
                $name = str(class_basename($model))->snake('-')->toString();
            }

            Route::model($name, $model);
        }
    }
}
