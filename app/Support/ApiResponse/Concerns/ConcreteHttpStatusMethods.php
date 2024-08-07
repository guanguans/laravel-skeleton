<?php

declare(strict_types=1);

namespace App\Support\ApiResponse\Concerns;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @mixin \App\Support\ApiResponse\ApiResponse
 */
trait ConcreteHttpStatusMethods
{
    public function ok(string $message = '', int $code = Response::HTTP_OK): JsonResponse
    {
        return $this->success(message: $message, code: $code);
    }

    public function created(mixed $data = null, string $message = '', ?string $location = null): JsonResponse
    {
        return $this->localize($data, $message, Response::HTTP_CREATED, $location);
    }

    public function accepted(mixed $data = null, string $message = '', ?string $location = null): JsonResponse
    {
        return $this->localize($data, $message, Response::HTTP_ACCEPTED, $location);
    }

    public function localize(mixed $data = null, string $message = '', int $code = Response::HTTP_OK, ?string $location = null): JsonResponse
    {
        return tap(
            $this->success($data, $message, $code),
            static function (JsonResponse $response) use ($location): void {
                $location and $response->header('Location', $location);
            }
        );
    }

    public function noContent(string $message = ''): JsonResponse
    {
        return $this->success(message: $message, code: Response::HTTP_NO_CONTENT);
    }

    public function badRequest(string $message = ''): JsonResponse
    {
        return $this->error($message);
    }

    public function unauthorized(string $message = ''): JsonResponse
    {
        return $this->error($message, Response::HTTP_UNAUTHORIZED);
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
