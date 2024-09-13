<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see https://github.com/laravelio/laravel.io
 */
class DisableFloc
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->header('Permissions-Policy', 'interest-cohort=()');

        return $response;
    }
}
