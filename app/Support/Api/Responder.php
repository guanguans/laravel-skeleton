<?php

declare(strict_types=1);

namespace App\Support\Api;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractCursorPaginator;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use Symfony\Component\HttpFoundation\Response;

class Responder
{
    public function success(mixed $data = [], string $message = '', int $code = 200): JsonResponse
    {
        return $this->json($data, $message, $code);
    }

    public function fail(string $message = '', int $code = 500, ?array $errors = null): JsonResponse
    {
        return $this->json(message: $message, code: $code, error: $errors);
    }

    public function exception(\Exception $throwable, int $code = 200): JsonResponse
    {
        return $this->fail($throwable->getMessage(), $code);
    }

    public function json(mixed $data = null, ?string $message = null, int $code = 200, ?array $error = null): JsonResponse
    {
        /** @see \Symfony\Component\HttpFoundation\Response::setStatusCode() */
        $statusCode = $this->statusCodeFor($code);
        if ($this->isInvalidFor($statusCode)) {
            throw new \InvalidArgumentException(sprintf('The HTTP status code "%s" is not valid.', $statusCode));
        }

        return new JsonResponse([
            'status' => $this->statusFor($statusCode),
            'code' => $code,
            'message' => $this->messageFor($message, $code),
            'data' => $this->dataFor($data),
            'error' => $this->errorFor($error),
        ]);
    }

    private function statusFor(int $statusCode): string
    {
        return match (true) {
            ($statusCode >= 400 && $statusCode < 500) => 'error', // client error
            ($statusCode >= 500 && $statusCode < 600) => 'fail', // service error
            default => 'success'
        };
    }

    private function statusCodeFor(int $code): int
    {
        return (int) substr((string) $code, 0, 3);
    }

    /**
     * @see \Symfony\Component\HttpFoundation\Response::setStatusCode()
     */
    private function messageFor(?string $message, int $code): string
    {
        if ($message) {
            return $message;
        }

        if (Lang::has($key = "http-business.$code")) {
            return __($key);
        }

        $statusCode = $this->statusCodeFor($code);
        if (Lang::has($key = "http-statuses.$statusCode")) {
            return __($key);
        }

        return Response::$statusTexts[$statusCode] ?? 'Unknown Status';
    }

    private function errorFor(?array $error): object|array
    {
        return $error ?: (object) [];
    }

    /**
     * @see \Symfony\Component\HttpFoundation\Response::isInvalid()
     */
    private function isInvalidFor(int $statusCode): bool
    {
        return $statusCode < 100 || $statusCode >= 600;
    }

    private function dataFor(mixed $data): array|object
    {
        return match (true) {
            $data instanceof ResourceCollection => $this->resourceCollection($data),
            $data instanceof JsonResource => $this->jsonResource($data),
            $data instanceof AbstractPaginator, $data instanceof AbstractCursorPaginator => $this->paginator($data),
            $data instanceof Arrayable, (\is_object($data) && method_exists($data, 'toArray')) => $data->toArray(),
            $data instanceof \JsonSerializable => $data->jsonSerialize(),
            empty($data) => (object) $data,
            default => Arr::wrap($data)
        };
    }

    /**
     * @@see \Illuminate\Http\Resources\Json\ResourceCollection::toResponse()
     */
    private function resourceCollection(ResourceCollection $resourceCollection): array
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
    private function jsonResource(JsonResource $jsonResource): array
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
    private function paginator(AbstractPaginator|AbstractCursorPaginator $paginator): array
    {
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
                    'links' => array_filter([
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
                    'links' => array_filter([
                        'previous' => $paginator->previousPageUrl(),
                        'next' => $paginator->nextPageUrl(),
                    ]),
                ],
            ],
            default => [],
        };
    }
}
