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
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class VerifyFormPassword
{
    /**
     * @noinspection RedundantDocCommentTagInspection
     *
     * @param \Closure(\Illuminate\Http\Request): (JsonResponse|RedirectResponse|Response) $next
     */
    public function handle(Request $request, \Closure $next, string $name = 'password'): SymfonyResponse
    {
        $request->whenFilled($name, static function () use ($request, $name): void {
            $request->validate([
                $name => Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ]);
        });

        return $next($request);
    }
}
