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
use Illuminate\Http\JsonResponse;

class ErrorPipe
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
    public function handle(array $data, \Closure $next, bool $hidden = false): JsonResponse
    {
        if ($hidden) {
            unset($data['error']);
        } else {
            $data['error'] = $data['error'] ?: (object) [];
        }

        return $next($data);
    }
}
