<?php

namespace App\Http\Middleware;

use App\Support\Attributes\Ignore;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use ReflectionMethod;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see https://blog.oussama-mater.tech/php-attributes/
 */
class IsRouteIgnored
{
    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route();

        if (! ($route instanceof Route) || $route->action['uses'] instanceof Closure) {
            return $next($request);
        }

        $reflection = new ReflectionMethod($route->getControllerClass(), $route->getActionMethod());

        $attributes = $reflection->getAttributes(Ignore::class);

        if ($attributes !== [] && \in_array(config('app.env'), $attributes[0]->newInstance()->in)) {
            abort(404);
        }

        return $next($request);
    }
}
