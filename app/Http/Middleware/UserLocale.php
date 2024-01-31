<?php

namespace App\Http\Middleware;

use Closure;

class UserLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function handle($request, Closure $next, ?string $guard = null)
    {
        $locale = auth($guard)->user()?->locale and app()->setLocale($locale);

        return $next($request);
    }
}
