<?php

declare(strict_types=1);

namespace App\Support\Api\Pipes;

use App\Support\Api\Support\Utils;
use Illuminate\Http\JsonResponse;

class SetStatusCodePipe extends Pipe
{
    /**
     * @param  \Closure(array): \Illuminate\Http\JsonResponse  $next
     */
    public function handle(array $data, \Closure $next, ?int $statusCode = null): JsonResponse
    {
        return $next($data)->setStatusCode($statusCode ?? Utils::statusCodeFor($data['code']));
    }
}
