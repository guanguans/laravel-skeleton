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

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BasicAuthentication
{
    public function handle(Request $request, \Closure $next): Response
    {
        if (!$request->hasHeader('Authorization')) {
            // Display login prompt
            header('WWW-Authenticate: Basic realm="Unauthorized"');
            abort(\Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED);
        }

        // Provided username or password does not match, throw an exception
        // Alternatively, the login prompt can be displayed once more
        abort_unless(
            $this->validateCredentials(explode(':', base64_decode(substr($request->header('Authorization'), 6), true))),
            \Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED
        );

        return $next($request);
    }

    /**
     * @noinspection SensitiveParameterInspection
     */
    private function validateCredentials(array $credentials): bool
    {
        // throw new RuntimeException('Not implemented');
        return (bool) $credentials;
    }
}
