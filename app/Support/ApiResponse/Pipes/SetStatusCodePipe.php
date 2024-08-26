<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\ApiResponse\Pipes;

use App\Support\ApiResponse\Pipes\Concerns\WithArgs;
use App\Support\ApiResponse\Support\Utils;
use Illuminate\Http\JsonResponse;

class SetStatusCodePipe
{
    use WithArgs;

    /**
     * @param  \Closure(array): \Illuminate\Http\JsonResponse  $next
     * @param  array{
     *  status: string,
     *  code: int,
     *  message: string,
     *  data: mixed,
     *  error: ?array,
     * }  $data
     */
    public function handle(array $data, \Closure $next, ?int $statusCode = null): JsonResponse
    {
        return $next($data)->setStatusCode($statusCode ?? Utils::statusCodeFor($data['code']));
    }
}
