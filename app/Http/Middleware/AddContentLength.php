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

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * @see https://github.com/ushahidi/platform/blob/develop/app/Http/Middleware/AddContentLength.php
 */
final class AddContentLength
{
    /**
     * @noinspection RedundantDocCommentTagInspection
     *
     * @param \Closure(\Illuminate\Http\Request): (JsonResponse|RedirectResponse|Response) $next
     */
    public function handle(Request $request, \Closure $next): SymfonyResponse
    {
        $response = $next($request);

        if (!$response->isEmpty() && !$response->headers->get('Content-Length')) {
            $response->headers->set(
                'Content-Length',
                // ensure that we get byte count by using 8bit encoding
                (string) mb_strlen($response->getContent(), '8bit')
            );
        }

        return $response->prepare($request);
    }
}
