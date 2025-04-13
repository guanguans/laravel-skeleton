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

class UserLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, \Closure $next, ?string $guard = null): Response
    {
        $locale = auth($guard)->user()?->locale and app()->setLocale($locale);

        return $next($request);
    }
}
