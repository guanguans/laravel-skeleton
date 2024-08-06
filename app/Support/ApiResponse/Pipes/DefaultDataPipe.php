<?php

declare(strict_types=1);

namespace App\Support\ApiResponse\Pipes;

use App\Support\ApiResponse\Pipes\Concerns\WithArgs;
use Illuminate\Http\JsonResponse;

class DefaultDataPipe
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
        $data['data'] = $this->dataFor($data['data']);

        return $next($data);
    }

    /**
     * @see \Illuminate\Routing\Router::toResponse()
     * @see \Illuminate\Http\JsonResponse::setData()
     */
    private function dataFor(mixed $data): array|object
    {
        return match (true) {
            ! (\is_array($data) || \is_object($data)) => (object) $data,
            default => $data
        };
    }
}
