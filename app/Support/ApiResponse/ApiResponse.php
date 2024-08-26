<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\ApiResponse;

use App\Support\ApiResponse\Concerns\ConcreteHttpStatusMethods;
use App\Support\ApiResponse\Concerns\HasExceptionMap;
use App\Support\ApiResponse\Concerns\HasPipes;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Dumpable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * @see https://github.com/dingo/api
 * @see https://github.com/f9webltd/laravel-api-response-helpers
 * @see https://github.com/flugg/laravel-responder
 * @see https://github.com/jiannei/laravel-response
 * @see https://github.com/MarcinOrlowski/laravel-api-response-builder
 *
 * @method array convertExceptionToArray(\Throwable $throwable)
 */
class ApiResponse implements Contracts\ApiResponse
{
    use ConcreteHttpStatusMethods;
    use Conditionable;
    use Dumpable;
    use HasExceptionMap;
    use HasPipes;
    use Macroable;
    use Tappable;

    public function __construct(?Collection $pipes = null, ?Collection $exceptionMap = null)
    {
        $this->pipes = collect($pipes);
        $this->exceptionMap = collect($exceptionMap);
    }

    public function success(mixed $data = null, string $message = '', int $code = Response::HTTP_OK): JsonResponse
    {
        return $this->json(true, $code, $message, $data);
    }

    public function error(string $message = '', int $code = Response::HTTP_BAD_REQUEST, ?array $error = null): JsonResponse
    {
        return $this->json(false, $code, $message, error: $error);
    }

    /**
     * @see \Illuminate\Foundation\Exceptions\Handler::render()
     * @see \Illuminate\Foundation\Exceptions\Handler::prepareException()
     * @see \Illuminate\Foundation\Exceptions\Handler::convertExceptionToArray()
     * @see \Illuminate\Database\QueryException
     */
    public function throw(\Throwable $throwable): JsonResponse
    {
        $newThrowable = $this->mapException($throwable);
        $newThrowable instanceof \Throwable and $throwable = $newThrowable;

        /** @noinspection PhpCastIsUnnecessaryInspection */
        $code = (int) $throwable->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR;
        $message = app()->hasDebugModeEnabled() ? $throwable->getMessage() : '';
        $error = (fn (): array => $this->convertExceptionToArray($throwable))->call(app(ExceptionHandler::class));
        $headers = [];

        if ($throwable instanceof HttpExceptionInterface) {
            $message = $throwable->getMessage();
            $code = $throwable->getStatusCode();
            $headers = $throwable->getHeaders();
        }

        if ($throwable instanceof ValidationException) {
            $message = $throwable->getMessage();
            $code = $throwable->status;
            $error = $throwable->errors();
        }

        if (\is_array($newThrowable) && $newThrowable) {
            $message = $newThrowable['message'] ?? null ?: $message;
            $code = $newThrowable['code'] ?? null ?: $code;
            $error = $newThrowable['error'] ?? null ?: $error;
            $headers = $newThrowable['headers'] ?? null ?: $headers;
        }

        return $this->error($message, $code, $error)->withHeaders($headers);
    }

    /**
     * @param  int<100, 599>|int<10000, 59999>  $code
     * @param  null|array<string, mixed>  $error
     */
    public function json(
        bool $status,
        int $code,
        string $message = '',
        mixed $data = null,
        ?array $error = null,
    ): JsonResponse {
        return (new Pipeline(app()))
            ->send(compact('status', 'code', 'message', 'data', 'error'))
            ->through($this->pipes())
            ->then(static fn (array $data): JsonResponse => new JsonResponse(
                data: $data,
                options: JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_LINE_TERMINATORS
                | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
            ));
    }
}
