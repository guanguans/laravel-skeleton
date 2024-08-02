<?php

declare(strict_types=1);

namespace App\Support\Api\Concerns;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @mixin \App\Support\Api\ApiResponse
 */
trait ConcreteStatus
{
    public function localize(int $code = 200): JsonResponse
    {
        return $this->ok(code: $code);
    }

    public function ok(string $message = '', int $code = 200): JsonResponse
    {
        return $this->success(message: $message, code: $code);
    }

    public function created(mixed $data = null, string $message = '', string $location = ''): JsonResponse
    {
        return tap(
            $this->success($data, $message, 201),
            static function (JsonResponse $response) use ($location): void {
                $location and $response->header('Location', $location);
            }
        );
    }

    public function accepted(mixed $data = null, string $message = '', string $location = ''): JsonResponse
    {
        return tap(
            $this->success($data, $message, 202),
            static function (JsonResponse $response) use ($location): void {
                $location and $response->header('Location', $location);
            }
        );
    }

    public function noContent(string $message = ''): JsonResponse
    {
        return $this->success(message: $message, code: 204);
    }

    public function badRequest(string $message = ''): JsonResponse
    {
        return $this->error($message, 400);
    }

    public function unauthorized(string $message = ''): JsonResponse
    {
        return $this->error($message, 401);
    }

    public function forbidden(string $message = ''): JsonResponse
    {
        return $this->error($message, Response::HTTP_FORBIDDEN);
    }

    public function notFound(string $message = ''): JsonResponse
    {
        return $this->error($message, Response::HTTP_NOT_FOUND);
    }

    public function methodNotAllowed(string $message = ''): JsonResponse
    {
        return $this->error($message, Response::HTTP_METHOD_NOT_ALLOWED);
    }
}
