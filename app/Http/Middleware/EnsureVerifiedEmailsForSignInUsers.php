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

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Webmozart\Assert\Assert;

/**
 * @see https://github.com/pinkary-project/pinkary.com
 */
final readonly class EnsureVerifiedEmailsForSignInUsers
{
    /**
     * @noinspection RedundantDocCommentTagInspection
     *
     * @param \Closure(\Illuminate\Http\Request): (JsonResponse|RedirectResponse|Response) $next
     */
    public function handle(Request $request, \Closure $next): SymfonyResponse
    {
        if (!auth()->check()) {
            return $next($request);
        }

        // $user = type($request->user())->as(User::class);
        Assert::isInstanceOf($user = $request->user(), User::class);

        if ($user->hasVerifiedEmail()) {
            return $next($request);
        }

        return to_route('verification.notice');
    }
}
