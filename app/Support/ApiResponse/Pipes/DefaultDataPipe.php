<?php

declare(strict_types=1);

namespace App\Support\ApiResponse\Pipes;

use App\Support\ApiResponse\Pipes\Concerns\WithArgs;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Router;

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
     * @see \Illuminate\Http\Resources\Json\ResourceCollection::toResponse()
     * @see \Illuminate\Http\Resources\Json\PaginatedResourceResponse::toResponse()
     * @see \Illuminate\Http\Resources\Json\JsonResource::toResponse()
     * @see \Illuminate\Http\Resources\Json\ResourceResponse::toResponse()
     * @see \Illuminate\Http\JsonResponse::setData()
     */
    private function dataFor(mixed $data): mixed
    {
        return match (true) {
            // $data instanceof \JsonSerializable => $data->jsonSerialize(),
            // $data instanceof Arrayable => $data->toArray(),
            ($response = Router::toResponse(request(), $data)) instanceof JsonResponse => $response->getData(),
            // ! \is_array($data) && ! \is_object($data) => (object) $data,
            default => $data
        };
    }
}
