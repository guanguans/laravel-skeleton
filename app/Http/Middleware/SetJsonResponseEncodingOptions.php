<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class SetJsonResponseEncodingOptions
{
    public function handle(Request $request, Closure $next, int $encodingOptions = JSON_UNESCAPED_UNICODE): Response
    {
        $response = $next($request);
        if ($response instanceof JsonResponse) {
            $response->setEncodingOptions($response->getEncodingOptions() | $encodingOptions);
        }

        return $response;
    }
}
