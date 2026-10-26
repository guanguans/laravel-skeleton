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

use App\Http\Middleware\LogHttp;
use App\Support\Monolog\Processor\AppendExtraDataProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Number;
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
             * @see \Guanguans\LaravelExceptionNotify\Collectors\ApplicationCollector
             * @see \Guanguans\LaravelExceptionNotify\Collectors\RequestBasicCollector
             * @see \Illuminate\Log\Context\ContextServiceProvider::boot()
             */
            Context::add([
                'Trace Id' => TRACE_ID,
                // 'Time' => Date::now()->format('Y-m-d H:i:s'),
                'Name' => config('app.name'),
                'Version' => app()->version(),
                'PHP Version' => \PHP_VERSION,
                'Php Interface' => \PHP_SAPI,
                'Environment' => app()->environment(),
                'In Console' => app()->runningInConsole(),
                'Debug Mode' => app()->hasDebugModeEnabled(),
                'Maintenance Mode' => app()->isDownForMaintenance(),
                'Timezone' => config('app.timezone'),
                'Locale' => config('app.locale'),
            ]);

            app()->runningInConsole() or Context::add([
                'User Id' => request()->user()?->id,
                'URL' => request()->url(),
                'IP' => request()->ip(),
                'Method' => request()->method(),
                'Controller Action' => request()->route()?->getActionName(),
                // 'Command' => null,
                // 'Memory' => Number::fileSize(memory_get_peak_usage(true)),
                // 'Duration' => blank(
                //     $startTime = \defined('LARAVEL_START') ? LARAVEL_START : request()->server('REQUEST_TIME_FLOAT')
                // ) ? 'Unknown' : humans_milliseconds((microtime(true) - (float) $startTime) * 1000),
            ]);

            LogHttp::skipWhen(
                fn (Request $request): bool => $this->app->runningUnitTests() /* || $request->isMethodSafe() */
            );
        });
    }

    /**
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    private function never(): void
    {
        $this->whenever(false, static function (): void {
            Log::withContext(request()->header());

            if (($logger = Log::getLogger()) instanceof MonologLogger) {
                $logger->pushProcessor(new AppendExtraDataProcessor(request()->header()));
            }
        });
    }
}
