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

use App\Models\JWTUser;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Traits\Conditionable;

class RouteServiceProvider extends ServiceProvider
{
    use Conditionable {
        Conditionable::when as whenever;
    }
    protected array $routeModels = [
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
            Route::patterns([
                'id' => '[0-9]+',
            ]);

            $this->bindRouteModels();
        });
    }

    private function never(): void
    {
        $this->whenever(false, static function (): void {
            URL::forceHttps();
            URL::forceScheme('https');
            app(Request::class)->server->set('HTTPS', 'on');
            app(Request::class)->server->set('SERVER_PORT', 443);
            Config::set('session.secure', true);

            Route::resourceVerbs([
                'create' => 'creator',
                'edit' => 'editor',
            ]);
        });
    }

    private function bindRouteModels(): void
    {
        Route::bind('user', static fn (mixed $value) => User::query()->where('id', $value)->firstOrFail());

        foreach ($this->routeModels as $name => $model) {
            if (\is_int($name)) {
                $name = str(class_basename($model))->snake('-')->toString();
            }

            Route::model($name, $model);
        }
    }
}
