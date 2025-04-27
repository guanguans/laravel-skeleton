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

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

final class BasicAuthentication
{
    /**
     * @noinspection RedundantDocCommentTagInspection
     *
     * @param \Closure(\Illuminate\Http\Request): (JsonResponse|RedirectResponse|Response) $next
     */
    public function handle(Request $request, \Closure $next): SymfonyResponse
    {
        if (!$request->hasHeader('Authorization')) {
            // Display login prompt
            header('WWW-Authenticate: Basic realm="Unauthorized"');
            abort(SymfonyResponse::HTTP_UNAUTHORIZED);
        }

        // Provided username or password does not match, throw an exception
        // Alternatively, the login prompt can be displayed once more
        abort_unless(
            $this->validateCredentials(explode(':', base64_decode(substr($request->header('Authorization'), 6), true))),
            SymfonyResponse::HTTP_UNAUTHORIZED
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
