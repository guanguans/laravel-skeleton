<?php

declare(strict_types=1);

namespace App\Support\ApiResponse\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Http\JsonResponse success(mixed $data = null, string $message = '', int $code = 200)
 * @method static \Illuminate\Http\JsonResponse error(string $message = '', int $code = 400, array|null $error = null)
 * @method static \Illuminate\Http\JsonResponse fail(string $message = '', int $code = 500, array|null $error = null)
 * @method static \Illuminate\Http\JsonResponse throw(\Throwable $throwable)
 * @method static \Illuminate\Http\JsonResponse json(string $status, int $code, string $message = '', mixed $data = null, array|null $error = null)
 * @method static \Illuminate\Http\JsonResponse localize(int $code = 200)
 * @method static \Illuminate\Http\JsonResponse ok(string $message = '', int $code = 200)
 * @method static \Illuminate\Http\JsonResponse created(mixed $data = null, string $message = '', string|null $location = null)
 * @method static \Illuminate\Http\JsonResponse accepted(mixed $data = null, string $message = '', string|null $location = null)
 * @method static \Illuminate\Http\JsonResponse noContent(string $message = '')
 * @method static \Illuminate\Http\JsonResponse badRequest(string $message = '')
 * @method static \Illuminate\Http\JsonResponse unauthorized(string $message = '')
 * @method static \Illuminate\Http\JsonResponse forbidden(string $message = '')
 * @method static \Illuminate\Http\JsonResponse notFound(string $message = '')
 * @method static \Illuminate\Http\JsonResponse methodNotAllowed(string $message = '')
 * @method static \App\Support\ApiResponse\ApiResponse|mixed when(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \App\Support\ApiResponse\ApiResponse|mixed unless(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \App\Support\ApiResponse\ApiResponse prependExceptionMap(string $exception, mixed $mapper)
 * @method static \App\Support\ApiResponse\ApiResponse putExceptionMap(string $exception, mixed $mapper)
 * @method static \App\Support\ApiResponse\ApiResponse extendExceptionMap(callable $callback)
 * @method static \App\Support\ApiResponse\ApiResponse tapExceptionMap(callable $callback)
 * @method static \App\Support\ApiResponse\ApiResponse unshiftPipes(void ...$pipes)
 * @method static \App\Support\ApiResponse\ApiResponse pushPipes(void ...$pipes)
 * @method static \App\Support\ApiResponse\ApiResponse extendPipes(callable $callback)
 * @method static \App\Support\ApiResponse\ApiResponse tapPipes(callable $callback)
 * @method static void tap(callable|null $callback = null)
 * @method static array convertExceptionToArray(\Throwable $throwable)
 *
 * @see \App\Support\ApiResponse\ApiResponse
 */
class ApiResponse extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Support\ApiResponse\ApiResponse::class;
    }
}
