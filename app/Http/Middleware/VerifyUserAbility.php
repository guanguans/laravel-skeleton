<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

/**
 * @see https://github.com/slimkit/plus/blob/2.4/app/Http/Middleware/UserAbility.php
 */
class VerifyUserAbility
{
    public function __construct(private readonly Guard $auth) {}

    /**
     * Handle an incoming request.
     *
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function handle(Request $request, Closure $next, string $ability, string $message = '')
    {
        if ($this->auth->guest() || ! $this->auth->user()?->ability($ability)) {
            abort(403, $message ?: '你没有权限执行该操作');
        }

        return $next($request);
    }
}
