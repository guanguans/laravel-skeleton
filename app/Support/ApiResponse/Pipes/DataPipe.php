<?php

declare(strict_types=1);

namespace App\Support\ApiResponse\Pipes;

use App\Support\ApiResponse\Pipes\Concerns\WithArgs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractCursorPaginator;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Routing\Router;

class DataPipe
{
    use WithArgs;

    /**
     * @param  array{
     *  status: string,
     *  code: int,
     *  message: string,
     *  data: mixed,
     *  error: ?array,
     * }  $data
     * @param  \Closure(array): \Illuminate\Http\JsonResponse  $next
     */
    public function handle(array $data, \Closure $next): JsonResponse
    {
        $data['data'] = $this->dataFor($data['data']);

        return $next($data);
    }

    /**
     * @see \Illuminate\Foundation\Exceptions\Handler::render()
     * @see \Illuminate\Routing\Router::toResponse()
     * @see \Illuminate\Http\Resources\Json\ResourceCollection::toResponse()
     * @see \Illuminate\Http\Resources\Json\JsonResource::toResponse()
     * @see \Illuminate\Http\Resources\Json\ResourceResponse::toResponse()
     * @see \Illuminate\Http\Resources\Json\PaginatedResourceResponse::toResponse()
     * @see \Illuminate\Pagination\Paginator::toArray()
     * @see \Illuminate\Pagination\LengthAwarePaginator::toArray()
     * @see \Illuminate\Pagination\CursorPaginator::toArray()
     * @see \Illuminate\Http\JsonResponse::setData()
     *
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    private function dataFor(mixed $data): mixed
    {
        return match (true) {
            $data instanceof AbstractCursorPaginator,
            $data instanceof AbstractPaginator => ResourceCollection::make($data)->toResponse(request())->getData(),
            ($response = Router::toResponse(request(), $data)) instanceof JsonResponse => $response->getData(),
            // ! \is_array($data) && ! \is_object($data) => (object) $data,
            default => $data
        };
    }
}
