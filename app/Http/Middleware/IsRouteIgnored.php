<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Http\Middleware;

use App\Support\Attribute\Ignore;
use App\Support\Trait\WithPipeArgs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Route;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * @see https://blog.oussama-mater.tech/php-attributes/
 */
final class IsRouteIgnored
{
    use WithPipeArgs;

    /**
     * @param \Closure(\Illuminate\Http\Request): (JsonResponse|RedirectResponse|Response) $next
     *
     * @throws \ReflectionException
     *
     * @noinspection RedundantDocCommentTagInspection
     */
    public function handle(Request $request, \Closure $next): SymfonyResponse
    {
        $route = $request->route();

        if (!($route instanceof Route) || $route->action['uses'] instanceof \Closure) {
            return $next($request);
        }

        $reflectionMethod = new \ReflectionMethod($route->getControllerClass(), $route->getActionMethod());

        $attributes = $reflectionMethod->getAttributes(Ignore::class);

        abort_if(
            [] !== $attributes && \in_array(config('app.env'), $attributes[0]->newInstance()->in, true),
            SymfonyResponse::HTTP_NOT_FOUND
        );

        return $next($request);
    }
}
