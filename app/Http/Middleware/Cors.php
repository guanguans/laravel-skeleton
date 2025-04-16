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

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class Cors
{
    /**
     * @noinspection RedundantDocCommentTagInspection
     *
     * @param \Closure(\Illuminate\Http\Request): \Symfony\Component\HttpFoundation\Response $next
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle(Request $request, \Closure $next, string $allowedOriginPatterns = '*'): Response
    {
        return tap($next($request), static function () use ($allowedOriginPatterns): void {
            if (class_exists(RequestHandled::class) && app()->bound('events')) {
                app()->make(Dispatcher::class)->listen(
                    RequestHandled::class,
                    static function (RequestHandled $event) use ($allowedOriginPatterns): void {
                        /**
                         * 仅设置 `Access-Control-Allow-Origin`， 其他由 @see \Illuminate\Http\Middleware\HandleCors 处理。
                         * 跨域访问的时候才会存在 `HTTP_ORIGIN` 字段。
                         */
                        Str::is(explode('|', $allowedOriginPatterns), $origin = $event->request->server('HTTP_ORIGIN', ''))
                            ? $event->response->headers->set('Access-Control-Allow-Origin', $origin)
                            : $event->response->headers->remove('Access-Control-Allow-Origin');
                    }
                );
            }
        });
    }
}
