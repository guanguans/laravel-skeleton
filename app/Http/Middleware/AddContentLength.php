<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see https://github.com/ushahidi/platform/blob/develop/app/Http/Middleware/AddContentLength.php
 */
class AddContentLength
{
    /**
     * Add content-length header to responses before being sent.
     * This only happens if it hasn't been already set.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $response->isEmpty() && ! $response->headers->get('Content-Length')) {
            $response->headers->set(
                'Content-Length',
                // ensure that we get byte count by using 8bit encoding
                mb_strlen($response->getContent(), '8bit')
            );
        }

        return $response->prepare($request);
    }
}
