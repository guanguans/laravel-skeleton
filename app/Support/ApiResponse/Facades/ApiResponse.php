<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\ApiResponse\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Http\JsonResponse success(mixed $data = null, string $message = '', int $code = 200)
 * @method static \Illuminate\Http\JsonResponse error(string $message = '', int $code = 400, array|null $error = null)
 * @method static \Illuminate\Http\JsonResponse throw(\Throwable $throwable)
 * @method static \Illuminate\Http\JsonResponse json(bool $status, int $code, string $message = '', mixed $data = null, array|null $error = null)
 * @method static \Illuminate\Http\JsonResponse ok(string $message = '', int $code = 200)
 * @method static \Illuminate\Http\JsonResponse created(mixed $data = null, string $message = '', string|null $location = null)
 * @method static \Illuminate\Http\JsonResponse accepted(mixed $data = null, string $message = '', string|null $location = null)
 * @method static \Illuminate\Http\JsonResponse localize(mixed $data = null, string $message = '', int $code = 200, string|null $location = null)
 * @method static \Illuminate\Http\JsonResponse noContent(string $message = '')
 * @method static \Illuminate\Http\JsonResponse badRequest(string $message = '')
 * @method static \Illuminate\Http\JsonResponse unauthorized(string $message = '')
 * @method static \Illuminate\Http\JsonResponse paymentRequired(string $message = '')
 * @method static \Illuminate\Http\JsonResponse forbidden(string $message = '')
 * @method static \Illuminate\Http\JsonResponse notFound(string $message = '')
 * @method static \Illuminate\Http\JsonResponse methodNotAllowed(string $message = '')
 * @method static \Illuminate\Http\JsonResponse requestTimeout(string $message = '')
 * @method static \Illuminate\Http\JsonResponse conflict(string $message = '')
 * @method static \Illuminate\Http\JsonResponse teapot(string $message = '')
 * @method static \Illuminate\Http\JsonResponse unprocessableEntity(string $message = '')
 * @method static \Illuminate\Http\JsonResponse tooManyRequests(string $message = '')
 * @method static \App\Support\ApiResponse\ApiResponse|mixed when(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \App\Support\ApiResponse\ApiResponse|mixed unless(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \App\Support\ApiResponse\ApiResponse prependExceptionMap(string $exception, \Throwable|array|(callable|array $mapper)
 * @method static \App\Support\ApiResponse\ApiResponse putExceptionMap(string $exception, \Throwable|array|(callable|array $mapper)
 * @method static \App\Support\ApiResponse\ApiResponse extendExceptionMap(callable $callback)
 * @method static \App\Support\ApiResponse\ApiResponse tapExceptionMap(callable $callback)
 * @method static \App\Support\ApiResponse\ApiResponse unshiftPipes(void ...$pipes)
 * @method static \App\Support\ApiResponse\ApiResponse pushPipes(void ...$pipes)
 * @method static \App\Support\ApiResponse\ApiResponse extendPipes(callable $callback)
 * @method static \App\Support\ApiResponse\ApiResponse tapPipes(callable $callback)
 * @method static void macro(string $name, object|callable $macro, object|callable $macro = null)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
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
