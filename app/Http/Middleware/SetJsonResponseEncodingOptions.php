<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class SetJsonResponseEncodingOptions
{
    public function handle(Request $request, Closure $next, $encodingOptions = JSON_UNESCAPED_UNICODE)
    {
        $response = $next($request);
        if ($response instanceof JsonResponse) {
            $response->setEncodingOptions($encodingOptions);
        }

        return $response;
    }
}
