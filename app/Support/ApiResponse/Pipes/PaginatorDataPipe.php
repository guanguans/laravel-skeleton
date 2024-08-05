<?php

declare(strict_types=1);

namespace App\Support\ApiResponse\Pipes;

use App\Support\ApiResponse\Pipes\Concerns\WithArgs;
use App\Support\ApiResponse\Support\Utils;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\AbstractCursorPaginator;
use Illuminate\Pagination\AbstractPaginator;

class PaginatorDataPipe
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
        if ($data['data'] instanceof AbstractPaginator || $data['data'] instanceof AbstractCursorPaginator) {
            $data['data'] = $this->paginatorFor($data['data']);
        }

        return $next($data);
    }

    /**
     * @see \Illuminate\Http\Resources\Json\PaginatedResourceResponse::toResponse()
     */
    private function paginatorFor(AbstractPaginator|AbstractCursorPaginator $paginator): array
    {
        /** @var \Illuminate\Pagination\Paginator $paginator */

        return [
            'data' => $paginator->toArray()['data'],
            'meta' => Utils::metaFor($paginator),
        ];
    }
}
