<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  array<string>|string  $allowedOriginPatterns
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle(Request $request, Closure $next, $allowedOriginPatterns = '*'): Response
    {
        return tap($next($request), static function () use ($allowedOriginPatterns) {
            if (class_exists(RequestHandled::class) && app()->bound('events')) {
                app()->make('events')->listen(RequestHandled::class,
                    function (RequestHandled $event) use ($allowedOriginPatterns) {
                        /** 仅设置 `Access-Control-Allow-Origin`, 其他由 @see \Fruitcake\Cors\HandleCors 处理. */
                        Str::is((array) explode('|', $allowedOriginPatterns),
                            $origin = $event->request->server('HTTP_ORIGIN', '')) // 跨域访问的时候才会存在 `HTTP_ORIGIN` 字段
                            ? $event->response->headers->set('Access-Control-Allow-Origin', $origin)
                            : $event->response->headers->remove('Access-Control-Allow-Origin');
                    });
            }
        });
    }
}
