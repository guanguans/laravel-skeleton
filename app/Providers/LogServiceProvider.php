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
use App\Support\Monolog\Processor\AppendExtraDataProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Traits\Conditionable;
use Monolog\Logger as MonologLogger;

class LogServiceProvider extends ServiceProvider
{
    use Conditionable {
        Conditionable::when as whenever;
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot(): void
    {
        $this->forever();
        $this->never();
        $this->unlessConsole();
    }

    private function unlessConsole(): void
    {
        $this->unless($this->app->runningInConsole(), static function (): void {
            Context::add([
                'user-id' => RequestFacade::user()?->id,
                'url' => RequestFacade::url(),
                'ip' => RequestFacade::ip(),
                'method' => RequestFacade::method(),
                'action' => RequestFacade::route()?->getActionName(),
            ]);
        });
    }

    private function forever(): void
    {
        Context::add([
            'php-version' => \PHP_VERSION,
            'php-interface' => \PHP_SAPI,
            'laravel-version' => $this->app->version(),
            'running-in-console' => $this->app->runningInConsole(),
            // self::REQUEST_ID_NAME => $this->app->make(self::REQUEST_ID_NAME),
        ]);

        LogHttp::skipWhen(fn (Request $request): bool => $this->app->runningUnitTests() || $request->isMethodSafe());
    }

    private function never(): void
    {
        $this->whenever(false, function (): void {
            // With context for current channel and stack.
            Log::withContext(RequestFacade::header());

            if (($logger = Log::getLogger()) instanceof MonologLogger) {
                $logger->pushProcessor(new AppendExtraDataProcessor(RequestFacade::header()));
            }
        });
    }
}
