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
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class VerifyCommonParameters
{
    /** @var list<string> */
    private static array $rules = [
        'signature' => 'required|string',
        'timestamp' => 'required|int',
        'nonce' => 'required|string|size:16',
    ];

    /**
     * @noinspection RedundantDocCommentTagInspection
     *
     * @param \Closure(\Illuminate\Http\Request): \Symfony\Component\HttpFoundation\Response $next
     */
    public function handle(Request $request, \Closure $next): Response
    {
        /** @noinspection PhpUndefinedMethodInspection */
        Validator::make($request->headers(), self::$rules)->validate();

        return $next($request);
    }
}
