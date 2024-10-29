<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\TrimStrings;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->booting(static function (Application $app): void {
        // $app->loadEnvironmentFrom(base_path('.env.').config('app.env'));
    })
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(static function (Middleware $middleware) {
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

        $middleware->redirectGuestsTo('/account/login');
        $middleware->redirectUsersTo(
            fn (Request $request): string => $request->user()->isAdmin()
                ? route('admin.dashboard')
                : route('account.dashboard')
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // $exceptions->dontFlash([]);
        $exceptions->shouldRenderJsonWhen(static function (Request $request, Throwable $e): bool {
            if ($request->is('api/*')) {
                return true;
            }

            return $request->expectsJson();
        });
    })->create();
