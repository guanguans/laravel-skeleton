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
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * @see https://github.com/slimkit/plus/blob/2.4/app/Http/Middleware/UserAbility.php
 */
final readonly class VerifyUserAbility
{
    use WithPipeArgs;

    public function __construct(private Guard $guard) {}

    /**
     * @param \Closure(\Illuminate\Http\Request): (JsonResponse|RedirectResponse|Response) $next
     *
     * @noinspection RedundantDocCommentTagInspection
     */
    public function handle(Request $request, \Closure $next, string $ability, string $message = ''): SymfonyResponse
    {
        abort_if(
            $this->guard->guest() || !$this->guard->user()?->can($ability),
            SymfonyResponse::HTTP_FORBIDDEN,
            $message ?: '你没有权限执行该操作'
        );

        return $next($request);
    }
}
