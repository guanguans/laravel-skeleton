<?php

declare(strict_types=1);

namespace App\Support\ApiResponse\Support;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Pagination\AbstractCursorPaginator;
use Illuminate\Support\Arr;

class Utils
{
    public static function statusCodeFor(int $code): int
    {
        return (int) substr((string) $code, 0, 3);
    }

    /**
     * @see \Illuminate\Http\Resources\Json\PaginatedResourceResponse::toResponse()
     * @see \Illuminate\Pagination\CursorPaginator::toArray()
     * @see \Illuminate\Pagination\LengthAwarePaginator::toArray()
     * @see \Illuminate\Pagination\Paginator::toArray()
     */
    public static function metaFor(object $paginator): array
    {
        return match (true) {
            $paginator instanceof AbstractCursorPaginator,
            $paginator instanceof LengthAwarePaginator,
            $paginator instanceof Paginator => Arr::except($paginator->toArray(), 'data'),
            default => (object) [],
        };
    }
}
