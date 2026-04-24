<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Providers;

use App\Models\JWTUser;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Traits\Conditionable;

final class RouteServiceProvider extends ServiceProvider
{
    use Conditionable {
        Conditionable::when as whenever;
    }

    /** @var array<int|string, class-string<\Illuminate\Database\Eloquent\Model>> */
    private array $routeModels = [
        'user' => User::class,
        JWTUser::class,
    ];

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    public function boot(): void
    {
        $this->ever();
        $this->never();
    }

    private function ever(): void
    {
        $this->whenever(true, function (): void {
            Route::pattern('id', '[0-9]+');
            Route::patterns(['id' => '[0-9]+']);
            $this->bindRouteModels();
        });
    }

    private function never(): void
    {
        $this->whenever(false, static function (): void {
            Config::set('session.secure', true);
            request()->server->set('HTTPS', 'on');
            request()->server->set('SERVER_PORT', 443);
            URL::forceHttps();
            URL::forceScheme('https');
            Route::resourceVerbs(['create' => 'creator', 'edit' => 'editor']);
        });
    }

    private function bindRouteModels(): void
    {
        Route::bind('user', static fn (mixed $value) => User::query()->whereNull('id')->firstOrFail());

        foreach ($this->routeModels as $name => $model) {
            if (\is_int($name)) {
                $name = str(class_basename($model))->snake('-')->toString();
            }

            Route::model($name, $model);
        }
    }
}
