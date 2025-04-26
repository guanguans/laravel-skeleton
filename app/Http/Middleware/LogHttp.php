<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Http\Middleware;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

final class LogHttp
{
    /** @var list<\Closure> */
    private static array $skipCallbacks = [];
    private static Logger $logger;
    private static string $level;

    /** @var list<string> */
    private static array $headerHidden = [
        'api-key',
        'authorization',
        'cookie',
        'token',
    ];

    /** @var list<string> */
    private static array $inputHidden = [
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
        app()->instance(self::class, $this);
    }

    /**
     * @see \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::skipWhen()
     */
    public static function skipWhen(\Closure $callback): void
    {
        self::$skipCallbacks[] = $callback;
    }

    public static function setLogger(LoggerInterface|string $logger): void
    {
        if (\is_string($logger)) {
            $logger = Log::channel($logger);
        }

        if (!$logger instanceof Logger) {
            $logger = new Logger($logger, app(Dispatcher::class));
        }

        self::$logger = $logger;
    }

    public static function setLevel(string $level): void
    {
        self::$level = $level;
    }

    /**
     * @noinspection RedundantDocCommentTagInspection
     *
     * @param \Closure(\Illuminate\Http\Request): (JsonResponse|RedirectResponse|Response) $next
     */
    public function handle(Request $request, \Closure $next, LoggerInterface|string $logger, string $level = 'info'): SymfonyResponse
    {
        self::setLogger($logger);
        self::setLevel($level);
        // $this->terminate($request, $next($request));

        return $next($request);
    }

    public function terminate(Request $request, SymfonyResponse $response): void
    {
        if ($this->shouldSkip($request)) {
            return;
        }

        self::$logger->log(
            self::$level,
            $this->messageFor($request, $response),
            $this->contextFor($request, $response)
        );
    }

    private function shouldSkip(Request $request): bool
    {
        foreach (self::$skipCallbacks as $callback) {
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
    private function messageFor(Request $request, SymfonyResponse $response): string
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
    private function contextFor(Request $request, SymfonyResponse $response): array
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

    /**
     * @noinspection SensitiveParameterInspection
     */
    private function headerFor(Request|SymfonyResponse $requestOrResponse): array
    {
        return collect($requestOrResponse->headers->all())
            ->map(static fn (array $header, string $key): string => Str::is(self::$headerHidden, $key) ? '***' : $header[0])
            ->all();
    }

    private function inputFor(array $input): array
    {
        return collect($input)
            ->map(static fn (mixed $value, string $key): mixed => Str::is(self::$inputHidden, $key) ? '***' : $value)
            ->all();
    }

    /**
     * @see \Symfony\Component\HttpFoundation\Response::setStatusCode()
     */
    private function statusTextFor(SymfonyResponse $response): string
    {
        return Response::$statusTexts[$response->getStatusCode()] ?? 'unknown status';
    }

    private function responseFor(SymfonyResponse $response): mixed
    {
        return $response instanceof JsonResponse ? $response->getData(true) : $response->getContent();
    }

    private function duration(): string
    {
        return number_format(microtime(true) - LARAVEL_START, 3);
    }
}
