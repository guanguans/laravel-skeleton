<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * ETag middleware.
 */
class ETag
{
    /**
     * Implement Etag support.
     *
     * @param \Illuminate\Http\Request $request the HTTP request
     * @param \Closure $next closure for the response
     */
    public function handle(Request $request, \Closure $next): mixed
    {
        // If this was not a get or head request, just return
        if (!$request->isMethod('get') && !$request->isMethod('head')) {
            return $next($request);
        }

        // Get the initial method sent by client
        $initialMethod = $request->method();

        // Force to get in order to receive content
        $request->setMethod('get');

        // Get response
        $response = $next($request);

        // Generate Etag
        $etag = md5(json_encode($response->headers->get('origin')).$response->getContent());

        // Load the Etag sent by client
        $requestEtag = str_replace('"', '', $request->getETags());

        // Check to see if Etag has changed
        if ($requestEtag && $requestEtag[0] === $etag) {
            $response->setNotModified();
        }

        // Set Etag
        $response->setEtag($etag);

        // Set back to original method
        $request->setMethod($initialMethod); // set back to original method

        // Send response
        return $response;
    }
}
