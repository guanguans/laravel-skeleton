<?php

declare(strict_types=1);

namespace App\Support\Api;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractCursorPaginator;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use Jiannei\Response\Laravel\Support\Facades\Format;
use Symfony\Component\HttpFoundation\Response;

class Responder
{
    public function fail(string $message = '', int $code = 500, mixed $errors = null): JsonResponse
    {
        // return Format::data(message: $message, code: $code, error: $errors)->response();

        return $this->json(message: $message, code: $code, error: $errors);
    }

    public function success(mixed $data = [], string $message = '', int $code = 200): JsonResponse
    {
        // return Format::data(data: $data, message: $message, code: $code)->response();

        return $this->json($data, $message, $code);
    }

    public function exception(\Exception $throwable, string $message = '', int $code = 200): JsonResponse
    {
        // return Format::data(data: $data, message: $message, code: $code)->response();

        return $this->fail($message, $code);
    }

    public function json(mixed $data = null, string $message = '', int $code = 200, mixed $error = null): JsonResponse
    {
        $statusCode = $this->statusCodeFor($code);

        return new JsonResponse([
            'status' => $this->statusFor($statusCode),
            'code' => $code,
            'message' => $this->messageFor($code, $message),
            'data' => $this->dataFor($data),
            'error' => $this->errorFor($error),
        ]);
    }

    private function statusCodeFor(int $code): int
    {
        return (int) substr((string) $code, 0, 3);
    }

    private function statusFor(int $statusCode): string
    {
        return match (true) {
            ($statusCode >= 400 && $statusCode <= 499) => 'error', // client error
            ($statusCode >= 500 && $statusCode <= 599) => 'fail', // service error
            default => 'success'
        };
    }

    private function dataFor(mixed $data): array|object
    {
        return match (true) {
            $data instanceof ResourceCollection => $this->resourceCollection($data),
            $data instanceof JsonResource => $this->jsonResource($data),
            $data instanceof AbstractPaginator || $data instanceof AbstractCursorPaginator => $this->paginator($data),
            $data instanceof Arrayable || (\is_object($data) && method_exists($data, 'toArray')) => $data->toArray(),
            empty($data) => (object) $data,
            default => Arr::wrap($data)
        };
    }

    private function messageFor(int $code, string $message = ''): ?string
    {
        if ($message) {
            return $message;
        }

        // todo lang
        return Response::$statusTexts[$this->statusCodeFor($code)] ?? 'Unknown Status';
    }

    private function errorFor(?array $error): object|array
    {
        return $error ?: (object) [];
    }
}
