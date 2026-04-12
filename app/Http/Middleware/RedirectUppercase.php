<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Http\Middleware;

use App\Support\Trait\WithPipeArgs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

final class RedirectUppercase
{
    use WithPipeArgs;

    /**
     * @param \Closure(\Illuminate\Http\Request): (JsonResponse|RedirectResponse|Response) $next
     *
     * @noinspection RedundantDocCommentTagInspection
     */
    public function handle(Request $request, \Closure $next): SymfonyResponse
    {
        if (($lower = strtolower($path = $request->path())) !== $path) {
            return redirect(url()->query($lower, $request->query()), SymfonyResponse::HTTP_MOVED_PERMANENTLY);
        }

        return $next($request);
    }
}
