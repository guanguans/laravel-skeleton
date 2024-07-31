<?php

declare(strict_types=1);

namespace App\Support\Api;

use Illuminate\Http\JsonResponse;

/**
 * @mixin \App\Support\Api\ApiResponse
 */
trait ConcreteJsonResponseMethods
{
    /**
     * Alias of the successful method, no need to specify the message and data parameters.
     */
    public function localize(int $code = 200): JsonResponse
    {
        return $this->ok(code: $code);
    }

    /**
     * Alias of success method, no need to specify data parameter.
     */
    public function ok(string $message = '', int $code = 200): JsonResponse
    {
        return $this->success(message: $message, code: $code);
    }

    /**
     * Respond with a created response and associate a location if provided.
     */
    public function created(mixed $data = null, string $message = '', string $location = ''): JsonResponse
    {
        return tap(
            $this->success($data, $message, 201),
            static function (JsonResponse $response) use ($location): void {
                $location and $response->header('Location', $location);
            }
        );
    }

    /**
     *  Respond with an accepted response and associate a location and/or content if provided.
     */
    public function accepted(mixed $data = null, string $message = '', string $location = ''): JsonResponse
    {
        return tap(
            $this->success($data, $message, 202),
            static function (JsonResponse $response) use ($location): void {
                $location and $response->header('Location', $location);
            }
        );
    }

    /**
     * Respond with a no content response.
     */
    public function noContent(string $message = ''): JsonResponse
    {
        return $this->success(message: $message, code: 204);
    }

    public function errorBadRequest(string $message = ''): JsonResponse
    {
        return $this->fail($message, 400);
    }

    public function errorUnauthorized(string $message = ''): JsonResponse
    {
        return $this->fail($message, 401);
    }

    public function errorForbidden(string $message = ''): JsonResponse
    {
        return $this->fail($message, 403);
    }

    public function errorNotFound(string $message = ''): JsonResponse
    {
        return $this->fail($message, 404);
    }

    public function errorMethodNotAllowed(string $message = ''): JsonResponse
    {
        return $this->fail($message, 405);
    }
}
