<?php

declare(strict_types=1);

namespace App\Support\Api;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractCursorPaginator;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ApiResponse
{
    use ConcreteJsonResponseMethods;

    /**
     * @var callable
     */
    private $tapper;

    public function __construct(?callable $tapper = null)
    {
        $this->tapper = $tapper ?? static function (JsonResponse $jsonResponse): void {};
    }

    public static function make(?callable $tapper = null): self
    {
        return new self($tapper);
    }

    public static function renderUsing(?callable $tapper = null): \Closure
    {
        return static function (\Throwable $throwable, Request $request) use ($tapper) {
            if ($request->is('api/*')) {
                return self::make($tapper)->exception($throwable);
            }
        };
    }

    public function success(mixed $data = null, string $message = '', int $code = 200): JsonResponse
    {
        return $this->json($data, $message, $code);
    }

    public function fail(string $message = '', int $code = 500, ?array $error = null): JsonResponse
    {
        return $this->json(message: $message, code: $code, error: $error);
    }

    /**
     * @see \Illuminate\Foundation\Exceptions\Handler::render()
     * @see \Illuminate\Foundation\Exceptions\Handler::prepareException()
     */
    public function exception(\Throwable $throwable): JsonResponse
    {
        $message = $throwable->getMessage();
        $code = $throwable->getCode() ?: 500;
        $headers = [];
        $error = $this->convertExceptionToArray($throwable);

        if ($throwable instanceof HttpExceptionInterface) {
            $code = $throwable->getStatusCode();
            $headers = $throwable->getHeaders();
        }

        if ($throwable instanceof ValidationException) {
            $code = $throwable->status;
            config('app.debug') and $error = $throwable->errors();
        }

        if ($throwable instanceof AuthenticationException) {
            $code = 401;
        }

        $message = $this->messageFor($message, $code) ?: 'Server Error';

        return $this->fail($message, $code, $error)->withHeaders($headers);
    }

    /**
     * @param  int<100, 599>|int<10000, 59999>  $code
     */
    public function json(
        mixed $data = null,
        string $message = '',
        int $code = 200,
        ?array $error = null,
        #[\SensitiveParameter] array $headers = [],
        int $options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_LINE_TERMINATORS
    ): JsonResponse {
        return tap(new JsonResponse(
            data: [
                'status' => $this->statusFor($this->statusCodeFor($code)),
                'code' => $code,
                'message' => $this->messageFor($message, $code),
                'data' => $this->dataFor($data),
                'error' => $this->errorFor($error),
            ],
            headers: $headers,
            options: $options
        ), $this->tapper);
    }

    private function statusFor(int $statusCode): string
    {
        return match (true) {
            $statusCode >= 400 && $statusCode < 500 => 'error', // client error
            $statusCode >= 500 && $statusCode < 600 => 'fail', // service error
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
    private function messageFor(string $message, int $code): string
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
     * @see \Illuminate\Routing\Router::toResponse()
     */
    private function dataFor(mixed $data): array|object
    {
        return match (true) {
            $data instanceof ResourceCollection => $this->resourceCollection($data),
            $data instanceof JsonResource => $this->jsonResource($data),
            $data instanceof AbstractPaginator, $data instanceof AbstractCursorPaginator => $this->paginator($data),
            $data instanceof Arrayable => $data->toArray(),
            $data instanceof \JsonSerializable => $data->jsonSerialize(),
            ! \is_object($data) => (object) $data,
            // default => Arr::wrap($data)
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
            default => [],
        };
    }

    /**
     * @see \Illuminate\Foundation\Exceptions\Handler::convertExceptionToArray()
     */
    private function convertExceptionToArray(\Throwable $throwable): array
    {
        return config('app.debug')
            ? [
                'message' => $throwable->getMessage(),
                'exception' => $throwable::class,
                'file' => $throwable->getFile(),
                'line' => $throwable->getLine(),
                'trace' => collect($throwable->getTrace())->map(static fn ($trace) => Arr::except($trace, ['args']))->all(),
            ]
            : [
                'message' => $throwable instanceof HttpExceptionInterface ? $throwable->getMessage() : 'Server Error',
            ];
    }
}
