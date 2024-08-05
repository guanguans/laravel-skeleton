<?php

declare(strict_types=1);

namespace App\Support\ApiResponse;

use App\Support\ApiResponse\Concerns\ConcreteHttpStatusMethods;
use App\Support\ApiResponse\Concerns\HasExceptionMap;
use App\Support\ApiResponse\Concerns\HasPipes;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Tappable;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * @see https://github.com/jiannei/laravel-response
 * @see https://github.com/f9webltd/laravel-api-response-helpers
 *
 * @method array convertExceptionToArray(\Throwable $throwable)
 */
class ApiResponse
{
    use ConcreteHttpStatusMethods;
    use Conditionable;
    use HasExceptionMap;
    use HasPipes;
    use Tappable;

    public function __construct(Collection $pipes, Collection $exceptionMap)
    {
        $this->pipes = $pipes;
        $this->exceptionMap = $exceptionMap;
    }

    public function success(mixed $data = null, string $message = '', int $code = Response::HTTP_OK): JsonResponse
    {
        return $this->json(status: __FUNCTION__, code: $code, message: $message, data: $data);
    }

    public function error(string $message = '', int $code = Response::HTTP_BAD_REQUEST, ?array $error = null): JsonResponse
    {
        return $this->json(status: __FUNCTION__, code: $code, message: $message, error: $error);
    }

    public function fail(string $message = '', int $code = Response::HTTP_INTERNAL_SERVER_ERROR, ?array $error = null): JsonResponse
    {
        return $this->json(status: __FUNCTION__, code: $code, message: $message, error: $error);
    }

    /**
     * @see \Illuminate\Foundation\Exceptions\Handler::render()
     * @see \Illuminate\Foundation\Exceptions\Handler::prepareException()
     */
    public function throw(\Throwable $throwable): JsonResponse
    {
        $message = $throwable->getMessage();

        /**
         * @see \Illuminate\Database\QueryException
         *
         * @noinspection PhpCastIsUnnecessaryInspection
         */
        $code = (int) $throwable->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR;
        $error = (fn (): array => $this->convertExceptionToArray($throwable))->call(app(ExceptionHandler::class));
        $headers = [];

        if ($throwable instanceof HttpExceptionInterface) {
            $code = $throwable->getStatusCode();
            $headers = $throwable->getHeaders();
        }

        if ($throwable instanceof ValidationException) {
            $code = $throwable->status;
            config('app.debug') and $error = $throwable->errors();
        }

        if ($map = $this->parseExceptionMap($throwable)) {
            $message = $map['message'] ?? null ?: $message;
            $code = $map['code'] ?? null ?: $code;
            $error = $map['error'] ?? null ?: $error;
            $headers = $map['headers'] ?? null ?: $headers;
        }

        return $this->fail($message, $code, $error)->withHeaders($headers);
    }

    /**
     * @param  int<100, 599>|int<10000, 59999>  $code
     * @param  array<string, mixed>|null  $error
     */
    public function json(
        string $status,
        int $code,
        string $message = '',
        mixed $data = null,
        ?array $error = null,
    ): JsonResponse {
        return (new Pipeline(app()))
            ->send(['status' => $status, 'code' => $code, 'message' => $message, 'data' => $data, 'error' => $error])
            ->through($this->pipes())
            ->then(static fn (array $data): JsonResponse => new JsonResponse(
                data: $data,
                options: JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_LINE_TERMINATORS
            ));
    }
}
