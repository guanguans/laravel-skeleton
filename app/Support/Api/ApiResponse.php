<?php

declare(strict_types=1);

namespace App\Support\Api;

use App\Support\Api\Concerns\ConcreteStatus;
use App\Support\Api\Concerns\HasPipes;
use App\Support\Api\Pipes\DataPipe;
use App\Support\Api\Pipes\ErrorPipe;
use App\Support\Api\Pipes\MessagePipe;
use App\Support\Api\Pipes\SetStatusCodePipe;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Pipeline\Pipeline;
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
    use ConcreteStatus;
    use Conditionable;
    use HasPipes;
    use Tappable;

    public function __construct(array $pipes = [])
    {
        $this->pipes = collect($pipes ?: $this->defaultPipes());
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
        $code = $throwable->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR;
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

        $exceptions = $this->exceptions();
        $message = $exceptions[$class = $throwable::class]['message'] ?? $message;
        $code = $exceptions[$class]['code'] ?? $code;

        return $this->fail($message, $code, $error)->withHeaders($headers);
    }

    /**
     * @param  int<100, 599>|int<10000, 59999>  $code
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
            ->through($this->pipes->all())
            ->then(static fn (array $data): JsonResponse => new JsonResponse(
                data: $data,
                options: JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_LINE_TERMINATORS
            ));
    }

    private function defaultPipes(): array
    {
        return [
            MessagePipe::class,
            DataPipe::class,
            ErrorPipe::class,
            SetStatusCodePipe::class,
        ];
    }

    /**
     * @todo from config
     */
    private function exceptions(): array
    {
        return [
            AuthenticationException::class => [
                'code' => Response::HTTP_UNAUTHORIZED,
            ],
        ];
    }
}
