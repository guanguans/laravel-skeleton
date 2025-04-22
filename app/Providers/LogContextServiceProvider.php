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

use App\Http\Middleware\LogHttp;
use App\Support\Contracts\ShouldRegisterContract;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;

class LogContextServiceProvider extends ServiceProvider implements ShouldRegisterContract
{
    use Conditionable {
        Conditionable::when as whenever;
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot(): void
    {
        Log::shareContext($this->sharedLogContext());
        $this->whenever(true, function (): void {
            // $this->app->instance(self::REQUEST_ID_NAME, (string) Str::uuid());
            // \Illuminate\Support\Facades\Request::getFacadeRoot()->headers->set(self::REQUEST_ID_NAME, $this->app->make(self::REQUEST_ID_NAME));
            // Context::add('request_id', $this->app->make(self::REQUEST_ID_NAME));

            // // With context for current channel and stack.
            // \Illuminate\Support\Facades\Log::withContext(\\Illuminate\Support\Facades\Request::getFacadeRoot()->headers());

            // if (($logger = \Illuminate\Support\Facades\Log::getLogger()) instanceof \Monolog\Logger) {
            //     $logger->pushProcessor(new AppendExtraDataProcessor(\\Illuminate\Support\Facades\Request::getFacadeRoot()->headers()));
            // }
            $this->preProcessRequest();
        });
        LogHttp::skipWhen(fn (\Illuminate\Http\Request $request): bool => $this->app->runningUnitTests() || $request->isMethodSafe());
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
                'user-id' => Request::getFacadeRoot()->user()?->id,
                'url' => Request::getFacadeRoot()->url(),
                'ip' => Request::getFacadeRoot()->ip(),
                'method' => Request::getFacadeRoot()->method(),
                // 'action' => \Illuminate\Support\Facades\Request::getFacadeRoot()->route()?->getActionName(),
            ])
        )->all();
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function preProcessRequest(): void
    {
        $request = $this->app->make(\Illuminate\Http\Request::class);

        // $request->headers->set(self::REQUEST_ID_NAME, $this->app->make(self::REQUEST_ID_NAME));

        if ($request->is('api/v1/*')) {
            $request->headers->set('Accept', 'application/json');
        }
    }
}
