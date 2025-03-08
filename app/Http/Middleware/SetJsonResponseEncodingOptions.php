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

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class SetJsonResponseEncodingOptions
{
    public function handle(Request $request, \Closure $next, int $encodingOptions = \JSON_UNESCAPED_UNICODE): Response
    {
        $response = $next($request);

        if ($response instanceof JsonResponse) {
            $response->setEncodingOptions($response->getEncodingOptions() | $encodingOptions);
        }

        return $response;
    }
}
