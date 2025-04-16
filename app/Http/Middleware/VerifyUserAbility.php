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
use Illuminate\Http\Request;

/**
 * @see https://github.com/slimkit/plus/blob/2.4/app/Http/Middleware/UserAbility.php
 */
class VerifyUserAbility
{
    public function __construct(private readonly Guard $auth) {}

    /**
     * @noinspection RedundantDocCommentTagInspection
     *
     * @param \Closure(\Illuminate\Http\Request): \Symfony\Component\HttpFoundation\Response $next
     */
    public function handle(Request $request, \Closure $next, string $ability, string $message = '')
    {
        abort_if(
            $this->auth->guest() || !$this->auth->user()?->ability($ability),
            403,
            $message ?: '你没有权限执行该操作'
        );

        return $next($request);
    }
}
