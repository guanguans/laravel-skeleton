<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractCursorPaginator;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\Lang;
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
    use Conditionable;
    use Tappable;

    private bool $restful = false;

    private array $headers = [];

    private int $encodingOptions = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_LINE_TERMINATORS;

    /**
     * @var array<class-string<\Throwable>, array{message: string, code: int}>
     */
    private array $exceptions = [
        AuthenticationException::class => [
            'code' => 401,
        ],
    ];

    /**
     * @var callable(\Illuminate\Http\JsonResponse): void
     */
    private $tapper;

    public function __construct(?callable $tapper = null)
    {
        $this->tapper = $tapper ?? static function (JsonResponse $jsonResponse): void {};
    }

    public static function make(?callable $tapper = null): self
    {
        return new self($tapper);
    }

    /**
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public static function registerDefaultRenderUsing(?callable $tapper): void
    {
        app(ExceptionHandler::class)->renderable(self::defaultRenderUsing($tapper));
    }

    /**
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public static function registerRenderUsing(?callable $tapper, \Closure $condition): void
    {
        app(ExceptionHandler::class)->renderable(self::renderUsing($tapper, $condition));
    }

    public static function defaultRenderUsing(?callable $tapper = null): \Closure
    {
        return self::renderUsing(
            $tapper,
            static fn (Request $request): bool => $request->is('api/*')
        );
    }

    /**
     * @noinspection PhpInconsistentReturnPointsInspection
     *
     * @see \Illuminate\Foundation\Exceptions\Handler::renderable()
     * @see \Illuminate\Foundation\Exceptions\Handler::renderViaCallbacks()
     */
    public static function renderUsing(?callable $tapper, \Closure $condition): \Closure
    {
        return static function (\Throwable $throwable, Request $request) use ($condition, $tapper) {
            if (value($condition, $request, $throwable)) {
                return self::make($tapper)->throw($throwable);
            }
        };
    }

    public function setRestful(bool $restful): self
    {
        $this->restful = $restful;

        return $this;
    }

    public function setHeaders(#[\SensitiveParameter] array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    public function setEncodingOptions(int $encodingOptions): self
    {
        $this->encodingOptions = $encodingOptions;

        return $this;
    }

    public function setTapper(callable $tapper): self
    {
        $this->tapper = $tapper;

        return $this;
    }

    public function setExceptions(array $exceptions): self
    {
        $this->exceptions = $exceptions;

        return $this;
    }

    /**
     * @param  class-string<\Throwable>  $exception
     * @param  array{message: string, code: int}  $properties
     */
    public function exception(string $exception, array $properties): self
    {
        $this->exceptions[$exception] = $properties;

        return $this;
    }

    /****************************************** start *****************************************/
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

    /****************************************** end *****************************************/

    public function success(mixed $data = null, string $message = '', int $code = 200): JsonResponse
    {
        return $this->json($data, $message, $code);
    }

    public function fail(string $message = '', int $code = 500, ?array $error = null): JsonResponse
    {
        return $this->json(message: $message, code: $code, error: $error);
    }

    /**
     * @see \Illuminate\Foundation\Exceptions\Handler::render()
     * @see \Illuminate\Foundation\Exceptions\Handler::prepareException()
     */
    public function throw(\Throwable $throwable): JsonResponse
    {
        $message = $throwable->getMessage();
        $code = $throwable->getCode() ?: 500;
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

        $message = $this->exceptions[$class = $throwable::class]['message'] ?? $message;
        $code = $this->exceptions[$class]['code'] ?? $code;

        return $this->fail($message, $code, $error)->withHeaders($headers);
    }

    /**
     * @param  int<100, 599>|int<10000, 59999>  $code
     */
    public function json(
        mixed $data = null,
        string $message = '',
        int $code = 200,
        ?array $error = null,
    ): JsonResponse {
        return tap(new JsonResponse(
            data: [
                'status' => $this->statusFor($code),
                'code' => $code,
                'message' => $message ?: $this->messageFor($code),
                'data' => $this->dataFor($data),
                'error' => $this->errorFor($error),
            ],
            status: $this->restful ? $this->statusCodeFor($code) : 200,
            headers: $this->headers,
            options: $this->encodingOptions,
        ), $this->tapper);
    }

    private function statusFor(int $code): string
    {
        $statusCode = $this->statusCodeFor($code);

        return match (true) {
            $statusCode >= 400 && $statusCode < 500 => 'error', // client error
            $statusCode >= 500 && $statusCode < 600 => 'fail', // service error
            default => 'success'
        };
    }

    private function statusCodeFor(int $code): int
    {
        return (int) substr((string) $code, 0, 3);
    }

    /**
     * @see \Symfony\Component\HttpFoundation\Response::setStatusCode()
     * @see \Illuminate\Foundation\Exceptions\Handler::prepareException()
     */
    private function messageFor(int $code): string
    {
        if (Lang::has($key = "http-business.$code")) {
            return __($key);
        }

        $statusCode = $this->statusCodeFor($code);
        if (Lang::has($key = "http-statuses.$statusCode")) {
            return __($key);
        }

        // ['Server Error', 'Unknown Status'];
        return Response::$statusTexts[$statusCode] ?? 'Whoops, looks like something went wrong.';
    }

    private function errorFor(?array $error): object|array
    {
        return $error ?: (object) [];
    }

    /**
     * @see \Illuminate\Routing\Router::toResponse()
     */
    private function dataFor(mixed $data): array|object
    {
        return match (true) {
            $data instanceof ResourceCollection => $this->resourceCollectionFor($data),
            $data instanceof JsonResource => $this->jsonResourceFor($data),
            $data instanceof AbstractPaginator, $data instanceof AbstractCursorPaginator => $this->paginatorFor($data),
            $data instanceof Arrayable => $data->toArray(),
            $data instanceof \JsonSerializable => $data->jsonSerialize(),
            ! \is_array($data) && ! \is_object($data) => (object) $data,
            default => $data
        };
    }

    /**
     * @@see \Illuminate\Http\Resources\Json\ResourceCollection::toResponse()
     */
    private function resourceCollectionFor(ResourceCollection $resourceCollection): array
    {
        return [
            'data' => $resourceCollection->resolve(),
            'meta' => array_merge_recursive(
                $this->metaFor($resourceCollection->resource),
                $resourceCollection->with(request()),
                $resourceCollection->additional
            ),
        ];
    }

    /**
     * @@see \Illuminate\Http\Resources\Json\ResourceResponse::toResponse()
     */
    private function jsonResourceFor(JsonResource $jsonResource): array
    {
        return array_merge_recursive(
            $jsonResource->resolve(),
            $jsonResource->with(request()),
            $jsonResource->additional
        );
    }

    /**
     * @see \Illuminate\Http\Resources\Json\PaginatedResourceResponse::toResponse()
     */
    private function paginatorFor(AbstractPaginator|AbstractCursorPaginator $paginator): array
    {
        /** @var \Illuminate\Pagination\Paginator $paginator */

        return [
            'data' => $paginator->toArray()['data'],
            'meta' => $this->metaFor($paginator),
        ];
    }

    /**
     * @see \Illuminate\Http\Resources\Json\PaginatedResourceResponse::toResponse()
     */
    private function metaFor(object $paginator): array
    {
        return match (true) {
            $paginator instanceof CursorPaginator => [
                'cursor' => [
                    'current' => $paginator->cursor()?->encode(),
                    'prev' => $paginator->previousCursor()?->encode(),
                    'next' => $paginator->nextCursor()?->encode(),
                    'count' => \count($paginator->items()),
                ],
            ],
            $paginator instanceof LengthAwarePaginator => [
                'pagination' => [
                    'count' => $paginator->lastItem(),
                    'per_page' => $paginator->perPage(),
                    'current_page' => $paginator->currentPage(),
                    'total' => $paginator->total(),
                    'total_pages' => $paginator->lastPage(),
                    'links' => ([
                        'previous' => $paginator->previousPageUrl(),
                        'next' => $paginator->nextPageUrl(),
                    ]),
                ],
            ],
            $paginator instanceof Paginator => [
                'pagination' => [
                    'count' => $paginator->lastItem(),
                    'per_page' => $paginator->perPage(),
                    'current_page' => $paginator->currentPage(),
                    'links' => ([
                        'previous' => $paginator->previousPageUrl(),
                        'next' => $paginator->nextPageUrl(),
                    ]),
                ],
            ],
            default => (object) [],
        };
    }
}
