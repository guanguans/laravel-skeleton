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

final class LogServiceProvider extends ServiceProvider
{
    use Conditionable {
        Conditionable::when as whenever;
    }

    public function boot(): void
    {
        $this->ever();
        $this->never();
    }

    private function ever(): void
    {
        $this->whenever(true, function (): void {
            /**
             * @see \Illuminate\Log\Context\ContextServiceProvider::boot()
             */
            Context::add([
                'php-version' => \PHP_VERSION,
                'php-interface' => \PHP_SAPI,
                'laravel-version' => $this->app->version(),
                'running-in-console' => $this->app->runningInConsole(),
                'trace-id' => TRACE_ID,
            ]);

            LogHttp::skipWhen(
                fn (Request $request): bool => $this->app->runningUnitTests() || $request->isMethodSafe()
            );

            $this->unlessConsole();
        });
    }

    /**
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    private function never(): void
    {
        $this->whenever(false, static function (): void {
            Log::withContext(RequestFacade::header());

            if (($logger = Log::getLogger()) instanceof MonologLogger) {
                $logger->pushProcessor(new AppendExtraDataProcessor(RequestFacade::header()));
            }
        });
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
}
