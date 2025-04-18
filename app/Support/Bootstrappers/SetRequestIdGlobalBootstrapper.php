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

namespace App\Support\Bootstrappers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see \Illuminate\Foundation\Bootstrap\SetRequestForConsole
 */
class SetRequestIdGlobalBootstrapper
{
    private const string X_REQUEST_ID_NAME = 'X-Request-Id';

    /**
     * Bootstrap the given application.
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function bootstrap(Application $app): void
    {
        \define('REQUEST_ID', (string) Str::uuid());
        \Illuminate\Support\Facades\Request::getFacadeRoot()->headers->set(self::X_REQUEST_ID_NAME, REQUEST_ID);
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
