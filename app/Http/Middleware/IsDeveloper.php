<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see https://github.com/MGeurts/genealogy/blob/main/app/Http/Middleware/IsDeveloper.php
 */
class IsDeveloper
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->is_developer) {
            return $next($request);
        }

        abort(403);
    }
}
