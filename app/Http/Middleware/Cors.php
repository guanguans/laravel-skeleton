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

use App\Support\Trait\WithPipeArgs;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

final class Cors
{
    use WithPipeArgs;

    /**
     * @param \Closure(\Illuminate\Http\Request): (JsonResponse|RedirectResponse|Response) $next
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * ```
     * \App\Http\Middleware\Cors::with('*.foo.com|*.bar.com');
     * ```
     *
     * @noinspection RedundantDocCommentTagInspection
     */
    public function handle(Request $request, \Closure $next, string $allowedOriginPatterns = '*'): SymfonyResponse
    {
        return tap(
            $next($request),
            static fn (): null => app()->make(Dispatcher::class)->listen(
                RequestHandled::class,
                /**
                 * 仅设置 `Access-Control-Allow-Origin`， 其他由 @see \Illuminate\Http\Middleware\HandleCors 处理。
                 * 跨域访问的时候才会存在 `HTTP_ORIGIN` 字段。
                 */
                static function (RequestHandled $requestHandled) use ($allowedOriginPatterns): void {
                    Str::is(explode('|', $allowedOriginPatterns), $origin = $requestHandled->request->server('HTTP_ORIGIN'))
                        ? $requestHandled->response->headers->set('Access-Control-Allow-Origin', $origin)
                        : $requestHandled->response->headers->remove('Access-Control-Allow-Origin');
                }
            )
        );
    }
}
