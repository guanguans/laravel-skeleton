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

use App\Console\Commands\ClearLogsCommand;
use App\Exceptions\Handler;
use Arifhp86\ClearExpiredCacheFile\ClearExpiredCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\TrimStrings;
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
    ->booting(static function (Application $app): void {
        // $app->loadEnvironmentFrom(base_path('.env.').config('app.env'));
        $app->singleton(Kernel::class, App\Http\Kernel::class);
        // $app->singleton(Illuminate\Contracts\Console\Kernel::class, App\Console\Kernel::class);
        $app->singleton(ExceptionHandler::class, Handler::class);
    })
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        apiPrefix: 'api/v1',
        // then: function (): void {
        //     /** @see https://github.com/packistry/packistry/blob/main/bootstrap/app.php */
        //     Route::middleware('web')->get('{any?}', fn () => response()
        //         ->file(public_path('index.html')))
        //         ->where('any', '.*');
        // },
    )
    ->withMiddleware(static function (Middleware $middleware): void {
        // $middleware->statefulApi();

        // $middleware->remove([
        //     ConvertEmptyStringsToNull::class,
        //     TrimStrings::class,
        // ]);

        // $middleware->convertEmptyStringsToNull(except: [
        //     static fn (Request $request): bool => $request->is('api/*'),
        // ]);
        // $middleware->trimStrings(except: [
        //     static fn (Request $request): bool => $request->is('api/*'),
        // ]);

        // $middleware->priority([
        //     Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        //     Illuminate\Cookie\Middleware\EncryptCookies::class,
        //     Illuminate\Session\Middleware\StartSession::class,
        //     Illuminate\View\Middleware\ShareErrorsFromSession::class,
        //     Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
        //     Illuminate\Routing\Middleware\SubstituteBindings::class,
        //     Illuminate\Auth\Middleware\Authorize::class,
        // ]);
        // $middleware->appendToPriorityList(
        //     [
        //         Illuminate\Cookie\Middleware\EncryptCookies::class,
        //         Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
        //     ],
        //     Illuminate\Routing\Middleware\ValidateSignature::class
        // );
        // $middleware->prependToPriorityList(
        //     [
        //         Illuminate\Cookie\Middleware\EncryptCookies::class,
        //         Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
        //     ],
        //     Illuminate\Routing\Middleware\ValidateSignature::class
        // );

        // $middleware->alias([
        //     'auth' => App\Http\Middleware\Authenticate::class,
        //     'auth.basic' => Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        // ]);

        // $middleware->prependToGroup('web', [
        //     App\Http\Middleware\EncryptCookies::class,
        //     Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        // ]);
        // $middleware->appendToGroup('api', [
        //     Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
        //     Illuminate\Routing\Middleware\SubstituteBindings::class,
        // ]);

        // $middleware->append(App\Http\Middleware\Localization::class);
        //
        // $middleware->validateSignatures(except: [
        //     'api/*',
        // ]);

        $middleware->validateCsrfTokens(except: [
            'livewire/*',
        ]);

        // $middleware->alias([
        //     'auth' => App\Http\Middleware\Authenticate::class,
        //     'guest' => App\Http\Middleware\RedirectIfAuthenticated::class,
        //     'role' => Spatie\Permission\Middleware\RoleMiddleware::class,
        //     'permission' => Spatie\Permission\Middleware\PermissionMiddleware::class,
        //     'role_or_permission' => Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        // ]);

        $middleware->web(append: [
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->redirectGuestsTo('/account/login');
        $middleware->redirectUsersTo(
            static fn (Request $request): string => $request->user()->isAdmin()
                ? route('admin.dashboard')
                : route('account.dashboard')
        );

        $middleware
            ->throttleApi(redis: true)
            ->trustProxies(at: [
                '127.0.0.1',
            ])
            ->api(prepend: [
                SetCacheHeaders::using('no_store'),
            ])
            ->append(SetCacheHeaders::using([
                'etag',
                'max_age' => 24 * 60 * 60,
                'private',
            ]));
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
        $exceptions->throttle(static fn (Throwable $throwable) => Lottery::odds(1, 1000));
        $exceptions->report(static function (QueryException $queryException): void {
            // dump($queryException->getRawSql());
        });
        // $exceptions->truncateRequestExceptionsAt(256);
        // $exceptions->dontTruncateRequestExceptions();
        // $exceptions->dontFlash([]);
        $exceptions->shouldRenderJsonWhen(static function (Request $request, Throwable $throwable): bool {
            if ($request->is('api/*')) {
                return true;
            }

            return $request->expectsJson();
        });
    })->create();
