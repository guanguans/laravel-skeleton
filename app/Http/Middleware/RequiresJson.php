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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

/**
 * @see https://masteringlaravel.io/daily/2024-07-15-how-to-enforce-all-api-requests-are-json
 */
class RequiresJson
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, \Closure $next): Response
    {
        throw_unless(
            $request->wantsJson(),
            NotAcceptableHttpException::class,
            'Please request with HTTP header: Accept: application/json'
        );

        return $next($request);
    }
}
