<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\TrimStrings;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
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
