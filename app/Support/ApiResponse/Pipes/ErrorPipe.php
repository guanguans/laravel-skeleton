<?php

declare(strict_types=1);

namespace App\Support\ApiResponse\Pipes;

use App\Support\ApiResponse\Pipes\Concerns\WithArgs;
use Illuminate\Http\JsonResponse;

class ErrorPipe
{
    use WithArgs;

    /**
     * @param array{
     *  status: string,
     *  code: int,
     *  message: string,
     *  data: mixed,
     *  error: ?array,
     * } $data
     * @param  \Closure(array): \Illuminate\Http\JsonResponse  $next
     */
    public function handle(array $data, \Closure $next): JsonResponse
    {
        $data['error'] = $data['error'] ?: (object) [];

        return $next($data);
    }
}
