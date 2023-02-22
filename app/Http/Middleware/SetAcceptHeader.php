<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetAcceptHeader
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $type = 'html')
    {
        $request->headers->set('Accept', "application/$type");

        return $next($request);
    }
}
