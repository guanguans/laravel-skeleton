<?php

namespace App\Bootstrappers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see \Illuminate\Foundation\Bootstrap\SetRequestForConsole
 */
class SetRequestId
{
    private const X_REQUEST_ID_NAME = 'X-Request-Id';

    /**
     * Bootstrap the given application.
     */
    public function bootstrap(Application $app): void
    {
        \define('REQUEST_ID', (string) Str::uuid());
        request()->headers->set(self::X_REQUEST_ID_NAME, REQUEST_ID);
        Http::globalOptions([
            'headers' => [
                self::X_REQUEST_ID_NAME => REQUEST_ID,
            ],
        ]);
        // $app->make(Kernel::class)->pushMiddleware(static function (Request $request, \Closure $next): Response {
        //     /** @var Response $response */
        //     $response = $next($request);
        //     $response->headers->set(self::X_REQUEST_ID_NAME, REQUEST_ID);
        //
        //     return $response;
        // });
    }
}
