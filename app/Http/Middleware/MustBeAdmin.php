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

class MustBeAdmin
{
    public function handle(Request $request, \Closure $next)
    {
        abort_if(auth()->guest() || !$request->user()->isAdmin(), 403, '非法访问！');

        return $next($request);
    }
}
