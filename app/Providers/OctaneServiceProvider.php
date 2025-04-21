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

use App\Support\Contracts\ShouldRegisterContract;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Laravel\Octane\Events\RequestReceived;
use Laravel\Octane\Events\RequestTerminated;

class OctaneServiceProvider extends ServiceProvider implements ShouldRegisterContract
{
    use Conditionable {
        Conditionable::when as whenever;
    }

    public function boot(): void
    {
        $this->when($this->isOctaneHttpServer(), function (): void {
            $this->app->get(Dispatcher::class)->listen(RequestReceived::class, static function (): void {
                $uuid = Str::uuid()->toString();

                if (config('octane.server') === 'roadrunner') {
                    Cache::put($uuid, microtime(true));

                    return;
                }

                Cache::store('octane')->put($uuid, microtime(true));
            });

            $this->app->get(Dispatcher::class)->listen(RequestTerminated::class, static function (): void {});
        });
    }

    public function shouldRegister(): bool
    {
        return true;
    }

    /**
     * Determine if server is running Octane.
     *
     * @noinspection GlobalVariableUsageInspection
     */
    private function isOctaneHttpServer(): bool
    {
        return isset($_SERVER['LARAVEL_OCTANE']) || isset($_ENV['OCTANE_DATABASE_SESSION_TTL']);
    }
}
