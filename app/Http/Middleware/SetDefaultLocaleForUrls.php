<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see https://www.harrisrafto.eu/simplifying-route-parameters-with-laravels-url-defaults/
 */
class SetDefaultLocaleForUrls
{
    public function handle(Request $request, Closure $next): Response
    {
        URL::defaults(['locale' => $request->user()->locale ?? config('app.locale')]);

        return $next($request);
    }
}
