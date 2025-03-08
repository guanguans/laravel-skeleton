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

use App\Support\Attributes\Ignore;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see https://blog.oussama-mater.tech/php-attributes/
 */
class IsRouteIgnored
{
    public function handle(Request $request, \Closure $next): Response
    {
        $route = $request->route();

        if (!($route instanceof Route) || $route->action['uses'] instanceof \Closure) {
            return $next($request);
        }

        $reflection = new \ReflectionMethod($route->getControllerClass(), $route->getActionMethod());

        $attributes = $reflection->getAttributes(Ignore::class);

        abort_if([] !== $attributes && \in_array(config('app.env'), $attributes[0]->newInstance()->in, true), 404);

        return $next($request);
    }
}
