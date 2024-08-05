<?php

declare(strict_types=1);

namespace App\Support\ApiResponse\Pipes;

use App\Support\ApiResponse\Pipes\Concerns\WithArgs;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractCursorPaginator;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\CursorPaginator;

class DataPipe
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
     */
    private function dataFor(mixed $data): array|object
    {
        return match (true) {
            $data instanceof ResourceCollection => $this->resourceCollectionFor($data),
            $data instanceof JsonResource => $this->jsonResourceFor($data),
            $data instanceof AbstractPaginator, $data instanceof AbstractCursorPaginator => $this->paginatorFor($data),
            $data instanceof Arrayable => $data->toArray(),
            $data instanceof \JsonSerializable => $data->jsonSerialize(),
            ! \is_array($data) && ! \is_object($data) => (object) $data,
            default => $data
        };
    }

    /**
     * @@see \Illuminate\Http\Resources\Json\ResourceCollection::toResponse()
     */
    private function resourceCollectionFor(ResourceCollection $resourceCollection): array
    {
        return [
            'data' => $resourceCollection->resolve(),
            'meta' => array_merge_recursive(
                $this->metaFor($resourceCollection->resource),
                $resourceCollection->with(request()),
                $resourceCollection->additional
            ),
        ];
    }

    /**
     * @@see \Illuminate\Http\Resources\Json\ResourceResponse::toResponse()
     */
    private function jsonResourceFor(JsonResource $jsonResource): array
    {
        return array_merge_recursive(
            $jsonResource->resolve(),
            $jsonResource->with(request()),
            $jsonResource->additional
        );
    }

    /**
     * @see \Illuminate\Http\Resources\Json\PaginatedResourceResponse::toResponse()
     */
    private function paginatorFor(AbstractPaginator|AbstractCursorPaginator $paginator): array
    {
        /** @var \Illuminate\Pagination\Paginator $paginator */

        return [
            'data' => $paginator->toArray()['data'],
            'meta' => $this->metaFor($paginator),
        ];
    }

    /**
     * @see \Illuminate\Http\Resources\Json\PaginatedResourceResponse::toResponse()
     */
    private function metaFor(object $paginator): array
    {
        return match (true) {
            $paginator instanceof CursorPaginator => [
                'cursor' => [
                    'current' => $paginator->cursor()?->encode(),
                    'prev' => $paginator->previousCursor()?->encode(),
                    'next' => $paginator->nextCursor()?->encode(),
                    'count' => \count($paginator->items()),
                ],
            ],
            $paginator instanceof LengthAwarePaginator => [
                'pagination' => [
                    'count' => $paginator->lastItem(),
                    'per_page' => $paginator->perPage(),
                    'current_page' => $paginator->currentPage(),
                    'total' => $paginator->total(),
                    'total_pages' => $paginator->lastPage(),
                    'links' => ([
                        'previous' => $paginator->previousPageUrl(),
                        'next' => $paginator->nextPageUrl(),
                    ]),
                ],
            ],
            $paginator instanceof Paginator => [
                'pagination' => [
                    'count' => $paginator->lastItem(),
                    'per_page' => $paginator->perPage(),
                    'current_page' => $paginator->currentPage(),
                    'links' => ([
                        'previous' => $paginator->previousPageUrl(),
                        'next' => $paginator->nextPageUrl(),
                    ]),
                ],
            ],
            default => (object) [],
        };
    }
}
