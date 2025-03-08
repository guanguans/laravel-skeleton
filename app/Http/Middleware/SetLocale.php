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

/**
 * @see https://github.com/MGeurts/genealogy/blob/main/app/Http/Middleware/SetLocale.php
 */
class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, \Closure $next): Response
    {
        if (!$request->user()) {
            return $next($request);
        }

        $language = $request->user()->language;

        if (isset($language)) {
            app()->setLocale($language);
        }

        return $next($request);
    }
}
