<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\TrimStrings;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->booting(static function (Application $app): void {
        // $app->loadEnvironmentFrom(base_path('.env.').config('app.env'));
    })
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        apiPrefix: 'api/v1',
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
            Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->redirectGuestsTo('/account/login');
        $middleware->redirectUsersTo(
            fn (Request $request): string => $request->user()->isAdmin()
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
    ->withExceptions(function (Exceptions $exceptions) {
        // $exceptions->truncateRequestExceptionsAt(256);
        // $exceptions->dontTruncateRequestExceptions();

        // $exceptions->dontFlash([]);
        $exceptions->shouldRenderJsonWhen(static function (Request $request, Throwable $e): bool {
            if ($request->is('api/*')) {
                return true;
            }

            return $request->expectsJson();
        });
    })->create();
