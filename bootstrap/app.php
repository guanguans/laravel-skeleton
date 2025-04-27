<?php

/** @noinspection PhpUnusedAliasInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

use App\Console\Commands\ClearLogsCommand;
use App\Http\Middleware\Localization;
use App\Http\Middleware\SetJsonResponseEncodingOptions;
use App\Listeners\PrepareRequestListener;
use App\Listeners\TraceEventListener;
use Arifhp86\ClearExpiredCacheFile\ClearExpiredCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Http\Request;
use Illuminate\Support\Lottery;
use jdavidbakr\LaravelCacheGarbageCollector\LaravelCacheGarbageCollector;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\ScheduleMonitor\Models\MonitoredScheduledTaskLogItem;
use Symfony\Component\Console\Output\ConsoleOutput;

return Application::configure(basePath: \dirname(__DIR__))
    ->booting(static function (Application $app): void {})
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        // apiPrefix: 'api/v1',
        then: static function (): void {
            // /** @see https://github.com/packistry/packistry/blob/main/bootstrap/app.php */
            // Route::middleware('web')->get('{any?}', fn () => response()
            //     ->file(public_path('index.html')))
            //     ->where('any', '.*');
        },
    )
    ->withEvents(false)
    ->withMiddleware(static function (Middleware $middleware): void {
        $middleware
            ->convertEmptyStringsToNull(except: [
                static fn (Request $request): bool => $request->is('api/*'),
            ])
            ->trimStrings(except: [
                static fn (Request $request): bool => $request->is('api/*'),
                'secret',
                'token',
            ])
            ->validateCsrfTokens(except: [
                'livewire/*',
            ])
            ->validateSignatures(except: [
                'livewire/*',
            ])
            ->api(
                append: [
                    SetJsonResponseEncodingOptions::class,
                ],
                prepend: [
                    SetCacheHeaders::using('no_store'),
                ]
            )
            ->web(
                append: [
                    AddLinkHeadersForPreloadedAssets::class,
                ],
            )
            ->append([
                Localization::class,
                SetCacheHeaders::using([
                    'etag',
                    'max_age' => 24 * 60 * 60,
                    'private',
                ]),
            ])
            ->redirectGuestsTo('/account/login')
            ->redirectUsersTo(
                static fn (Request $request): string => $request->user()->isAdmin()
                    ? route('admin.dashboard')
                    : route('account.dashboard')
            )
            ->statefulApi()
            ->throttleApi(/* redis: true */)
            ->trustProxies(at: [
                '127.0.0.1',
            ]);
    })
    ->withSchedule(static function (Schedule $schedule): void {
        $schedule->command('inspire')->everyMinute()->withoutOverlapping(60);
        // $schedule->command('inspire')->daily()->atRandom('07:15', '11:42')->withoutOverlapping(60);
        // $schedule->command('backup:clean')->daily()->at('05:15')->withoutOverlapping();
        // $schedule->command('backup:run')->daily()->at('05:30')->withoutOverlapping();
        // $schedule->command('backup:monitor')->daily()->at('05:45')->withoutOverlapping();
        // $schedule->command('model:prune', ['--model' => MonitoredScheduledTaskLogItem::class])->daily()->withoutOverlapping();
        // $schedule->command('telescope:prune')->daily()->skip(app()->isProduction())->withoutOverlapping();
        // $schedule->command(ClearLogsCommand::class)->daily()->withoutOverlapping();
        // $schedule->command(ClearExpiredCommand::class)->daily()->withoutOverlapping();
        // $schedule->command('disposable:update')->weekly()->at('04:00');
        // $schedule->command('db:monitor', ['--databases' => 'mysql', '--max' => 100])->userAppendOutputToDaily()->everyMinute();
        // $schedule->command(RunHealthChecksCommand::class)->everyMinute();
        // $schedule->command(LaravelCacheGarbageCollector::class)->daily();
        // $schedule->job(function (ConsoleOutput $consoleOutput): void {
        //     $consoleOutput->writeln(Inspiring::quote());
        // })->everyMinute();
        // $schedule->call(function (ConsoleOutput $consoleOutput): void {
        //     $consoleOutput->writeln(Inspiring::quote());
        // })->everyMinute();
        // $schedule->exec('php', ['-v'])->everyMinute();
    })
    ->withExceptions(static function (Exceptions $exceptions): void {
        $exceptions
            ->throttle(static fn (Throwable $throwable) => Lottery::odds(1, 1000))
            ->truncateRequestExceptionsAt(256)
            ->dontTruncateRequestExceptions()
            ->dontReport([
            ])
            ->dontFlash([
            ])
            ->shouldRenderJsonWhen(static function (Request $request): bool {
                if ($request->is('api/*')) {
                    return true;
                }

                return $request->expectsJson();
            });

        $exceptions->reportable(static function (QueryException $queryException): void {});
        $exceptions->renderable(static function (QueryException $queryException): void {});
    })
    ->create()
    ->tap(static function (Application $app): void {
        $app->afterLoadingEnvironment((new PrepareRequestListener)(...));
        $app->make(DispatcherContract::class)->listen('*', TraceEventListener::class);
    });
