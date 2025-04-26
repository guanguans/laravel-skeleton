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
    /**
     * @noinspection SensitiveParameterInspection
     */
    public function __construct(private Guard $auth) {}

    /**
     * @noinspection RedundantDocCommentTagInspection
     *
     * @param \Closure(\Illuminate\Http\Request): (JsonResponse|RedirectResponse|Response) $next
     */
    public function handle(Request $request, \Closure $next, string $ability, string $message = ''): SymfonyResponse
    {
        abort_if(
            $this->auth->guest() || !$this->auth->user()?->can($ability),
            403,
            $message ?: '你没有权限执行该操作'
        );

        return $next($request);
    }
}
