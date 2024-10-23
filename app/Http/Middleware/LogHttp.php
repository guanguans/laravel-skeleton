<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class LogHttp
{
    /**
     * @var array<\Closure>
     */
    private static array $skipCallbacks = [];

    private static Logger $logger;

    private static string $level;

    /**
     * @var array<string>
     */
    private array $headerHidden = [
        'api-key',
        'authorization',
        'cookie',
        'token',
    ];

    /**
     * @var array<string>
     */
    private array $inputHidden = [
        '*password',
        '*password*',
        'password',
        'password*',
    ];

    public function __construct()
    {
        /**
         * 默认情况下，Laravel 会为 terminate 方法解析中间件的新实例。
         * 需要保持 handle 和 terminate 之间的状态。
         *
         * @see https://www.harrisrafto.eu/mastering-laravels-terminable-middleware-post-response-magic/
         */
        app()->instance(static::class, $this);
    }

    /**
     * @see \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::skipWhen()
     */
    public static function skipWhen(Closure $callback): void
    {
        static::$skipCallbacks[] = $callback;
    }

    public static function setLogger(string|LoggerInterface $logger): void
    {
        if (\is_string($logger)) {
            $logger = Log::channel($logger);
        }

        if (! $logger instanceof Logger) {
            $logger = new Logger($logger, app(Dispatcher::class));
        }

        static::$logger = $logger;
    }

    public static function setLevel(string $level): void
    {
        static::$level = $level;
    }

    public function handle(Request $request, Closure $next, string|LoggerInterface $logger, string $level = 'info'): Response
    {
        static::setLogger($logger);
        static::setLevel($level);
        // $this->terminate($request, $next($request));

        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        if ($this->shouldSkip($request)) {
            return;
        }

        static::$logger->log(
            static::$level,
            $this->messageFor($request, $response),
            $this->contextFor($request, $response)
        );
    }

    private function shouldSkip(Request $request): bool
    {
        foreach (static::$skipCallbacks as $callback) {
            if ($callback($request)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @see \Symfony\Component\HttpFoundation\Request::__toString()
     * @see \Symfony\Component\HttpFoundation\Response::__toString()
     */
    private function messageFor(Request $request, Response $response): string
    {
        return \sprintf(
            '%s %s %s -> HTTP/%s %s %s',
            $request->method(),
            $request->path(),
            $request->getProtocolVersion(),
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $this->statusTextFor($response)
        );
    }

    /**
     * @noinspection GlobalVariableUsageInspection
     */
    private function contextFor(Request $request, Response $response): array
    {
        return [
            'method' => $request->method(),
            'path' => $request->path(),
            'request_header' => $this->headerFor($request),
            // 'files' => $request->allFiles(),
            'files' => $_FILES,
            'post' => $this->inputFor($request->post()),
            'query' => $request->query(),
            'input' => $this->inputFor($request->input()),
            'status_code' => $response->getStatusCode(),
            'status_text' => $this->statusTextFor($response),
            'response_header' => $this->headerFor($response),
            'response' => $this->responseFor($response),
            'ip' => $request->getClientIp(),
            'duration' => $this->duration(),
        ];
    }

    private function headerFor(Response|Request $requestOrResponse): array
    {
        return collect($requestOrResponse->headers->all())
            ->map(fn (array $header, string $key): string => Str::is($this->headerHidden, $key) ? '***' : $header[0])
            ->all();
    }

    private function inputFor(array $input): array
    {
        return collect($input)
            ->map(fn (mixed $value, string $key): mixed => Str::is($this->inputHidden, $key) ? '***' : $value)
            ->all();
    }

    /**
     * @see \Symfony\Component\HttpFoundation\Response::setStatusCode()
     */
    private function statusTextFor(Response $response): string
    {
        return Response::$statusTexts[$response->getStatusCode()] ?? 'unknown status';
    }

    private function responseFor(Response $response): mixed
    {
        return $response instanceof JsonResponse ? $response->getData(true) : $response->getContent();
    }

    private function duration(): string
    {
        return number_format(microtime(true) - LARAVEL_START, 3);
    }
}
