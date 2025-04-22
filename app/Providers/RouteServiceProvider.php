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
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Traits\Conditionable;

class RouteServiceProvider extends ServiceProvider
{
    use Conditionable {
        Conditionable::when as whenever;
    }
    protected array $routeModels = [
        'user' => User::class,
    ];

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    public function boot(): void
    {
        Route::pattern('id', '[0-9]+');
        $this->bindRouteModels();
        // Route::resourceVerbs([
        //     'create' => 'crear',
        //     'edit' => 'editar',
        // ]);
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
