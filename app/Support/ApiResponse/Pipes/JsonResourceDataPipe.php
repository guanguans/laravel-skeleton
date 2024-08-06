<?php

declare(strict_types=1);

namespace App\Support\ApiResponse\Pipes;

use App\Support\ApiResponse\Pipes\Concerns\WithArgs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @method array wrap(\Illuminate\Support\Collection|array $data, array $with = [], array $additional = [])
 */
class JsonResourceDataPipe
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
        if ($data['data'] instanceof JsonResource) {
            $data['data'] = $this->jsonResourceFor($data['data']);
        }

        return $next($data);
    }

    /**
     * @see \Illuminate\Http\Resources\Json\JsonResource::toResponse()
     * @see \Illuminate\Http\Resources\Json\ResourceResponse::toResponse()
     * @see \Illuminate\Http\Resources\Json\ResourceResponse::wrap()
     */
    private function jsonResourceFor(JsonResource $jsonResource): \stdClass
    {
        return $jsonResource->response()->getData();
    }
}
