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
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class LogContextServiceProvider extends ServiceProvider implements ShouldRegisterContract
{
    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot(): void
    {
        Log::shareContext($this->sharedLogContext());
    }

    public function shouldRegister(): bool
    {
        return true;
    }

    /**
     * @throws BindingResolutionException
     */
    private function sharedLogContext(): array
    {
        return collect([
            'php-version' => \PHP_VERSION,
            'php-interface' => \PHP_SAPI,
            'laravel-version' => $this->app->version(),
            'running-in-console' => $this->app->runningInConsole(),
            // self::REQUEST_ID_NAME => $this->app->make(self::REQUEST_ID_NAME),
        ])->unless(
            $this->app->runningInConsole(),
            static fn (Collection $context): Collection => $context->merge([
                'user-id' => \Illuminate\Support\Facades\Request::getFacadeRoot()->user()?->id,
                'url' => \Illuminate\Support\Facades\Request::getFacadeRoot()->url(),
                'ip' => \Illuminate\Support\Facades\Request::getFacadeRoot()->ip(),
                'method' => \Illuminate\Support\Facades\Request::getFacadeRoot()->method(),
                // 'action' => \Illuminate\Support\Facades\Request::getFacadeRoot()->route()?->getActionName(),
            ])
        )->all();
    }
}
