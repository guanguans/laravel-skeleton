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
use Symfony\Component\HttpFoundation\Response;

class VerifyFormPaginate
{
    public function handle(
        Request $request,
        \Closure $next,
        int $maxPerPage = 100,
        string $perPageName = 'per_page',
    ): Response {
        $request->whenFilled($perPageName, static function () use ($request, $perPageName, $maxPerPage): void {
            $request->validate([
                $perPageName => "max:$maxPerPage",
            ]);
        });

        return $next($request);
    }
}
