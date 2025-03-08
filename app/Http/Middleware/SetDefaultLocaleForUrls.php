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
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see https://www.harrisrafto.eu/simplifying-route-parameters-with-laravels-url-defaults/
 */
class SetDefaultLocaleForUrls
{
    public function handle(Request $request, \Closure $next): Response
    {
        URL::defaults(['locale' => $request->user()->locale ?? config('app.locale')]);

        return $next($request);
    }
}
