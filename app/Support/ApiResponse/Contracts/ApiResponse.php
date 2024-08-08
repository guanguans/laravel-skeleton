<?php

declare(strict_types=1);

namespace App\Support\ApiResponse\Contracts;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see https://github.com/dingo/api
 * @see https://github.com/f9webltd/laravel-api-response-helpers
 * @see https://github.com/flugg/laravel-responder
 * @see https://github.com/jiannei/laravel-response
 * @see https://github.com/MarcinOrlowski/laravel-api-response-builder
 */
interface ApiResponse
{
    public function success(mixed $data = null, string $message = '', int $code = Response::HTTP_OK): JsonResponse;

    public function error(string $message = '', int $code = Response::HTTP_BAD_REQUEST, ?array $error = null): JsonResponse;

    /**
     * @see \Illuminate\Foundation\Exceptions\Handler::render()
     * @see \Illuminate\Foundation\Exceptions\Handler::prepareException()
     * @see \Illuminate\Foundation\Exceptions\Handler::convertExceptionToArray()
     * @see \Illuminate\Database\QueryException
     */
    public function throw(\Throwable $throwable): JsonResponse;

    /**
     * @param  int<100, 599>|int<10000, 59999>  $code
     * @param  array<string, mixed>|null  $error
     */
    public function json(bool $status, int $code, string $message = '', mixed $data = null, ?array $error = null): JsonResponse;
}
