<?php

declare(strict_types=1);

namespace App\Support\ApiResponse\Pipes;

use Illuminate\Http\JsonResponse;

class ErrorPipe extends Pipe
{
    /**
     * @param  \Closure(array): \Illuminate\Http\JsonResponse  $next
     */
    public function handle(array $data, \Closure $next): JsonResponse
    {
        $data['error'] = $data['error'] ?: (object) [];

        return $next($data);
    }
}
