<?php

declare(strict_types=1);

namespace App\Support\ApiResponse\Pipes;

use App\Support\ApiResponse\Pipes\Concerns\WithArgs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ResourceCollectionDataPipe
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
        if ($data['data'] instanceof ResourceCollection) {
            $data['data'] = $this->resourceCollectionFor($data['data']);
        }

        return $next($data);
    }

    /**
     * @see \Illuminate\Http\Resources\Json\ResourceCollection::toResponse()
     * @see \Illuminate\Http\Resources\Json\PaginatedResourceResponse::toResponse()
     */
    private function resourceCollectionFor(ResourceCollection $resourceCollection): \stdClass
    {
        return $resourceCollection->response()->getData();
    }
}
