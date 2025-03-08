<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

/**
 * @see https://masteringlaravel.io/daily/2024-07-15-how-to-enforce-all-api-requests-are-json
 */
class RequiresJson
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        throw_unless($request->wantsJson(),
            NotAcceptableHttpException::class,
            'Please request with HTTP header: Accept: application/json');

        return $next($request);
    }
}
