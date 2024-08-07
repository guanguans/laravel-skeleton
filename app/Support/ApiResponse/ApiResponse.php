<?php

declare(strict_types=1);

namespace App\Support\ApiResponse;

use App\Support\ApiResponse\Concerns\ConcreteHttpStatusMethods;
use App\Support\ApiResponse\Concerns\HasExceptionMap;
use App\Support\ApiResponse\Concerns\HasPipes;
use App\Support\ApiResponse\Support\Utils;
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
 * @see https://github.com/dingo/api
 * @see https://github.com/f9webltd/laravel-api-response-helpers
 * @see https://github.com/jiannei/laravel-response
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
        return $this->json(__FUNCTION__, $code, $message, $data);
    }

    public function error(string $message = '', int $code = Response::HTTP_BAD_REQUEST, ?array $error = null): JsonResponse
    {
        return $this->json(__FUNCTION__, $code, $message, error: $error);
    }

    public function fail(string $message = '', int $code = Response::HTTP_INTERNAL_SERVER_ERROR, ?array $error = null): JsonResponse
    {
        return $this->json(__FUNCTION__, $code, $message, error: $error);
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

        $message = config('app.debug') ? $throwable->getMessage() : '';
        $code = transform($throwable, static function (\Throwable $throwable): int {
            $code = $throwable->getCode();
            if (\is_string($code)) {
                preg_match_all('/\d+/', $code, $matches);
                $code = implode('', $matches[0] ?? []);
            }

            return (int) $code ?: Response::HTTP_INTERNAL_SERVER_ERROR;
        });
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

        $statusCode = Utils::statusCodeFor($code);

        return $this
            ->{$statusCode >= 400 && $statusCode < 500 ? 'error' : 'fail'}($message, $code, $error)
            ->withHeaders($headers);
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
            ->send(compact('status', 'code', 'message', 'data', 'error'))
            ->through($this->pipes())
            ->then(static fn (array $data): JsonResponse => new JsonResponse(
                data: $data,
                options: JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_LINE_TERMINATORS
            ));
    }
}
