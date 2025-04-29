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

use App\Support\Traits\WithPipeArgs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * @see https://github.com/TheDragonCode/laravel-http-logger
 */
final class LogHttp
{
    use WithPipeArgs;

    /** @var list<\Closure> */
    private static array $skipCallbacks = [];

    /** @var list<string> */
    private static array $headerHidden = [
        'api-key',
        'authorization',
        'cookie',
        'set-cookie',
        'token',
        'x-xsrf-token',
        'access_token',
    ];

    /** @var list<string> */
    private static array $postHidden = [
        '*password',
        '*password*',
        'password',
        'password*',
        'password_confirmation',
    ];
    private Logger $logger;
    private string $level;

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

    /**
     * @noinspection RedundantDocCommentTagInspection
     *
     * @param \Closure(\Illuminate\Http\Request): (JsonResponse|RedirectResponse|Response) $next
     */
    public function handle(
        Request $request,
        \Closure $next,
        null|LoggerInterface|string $logger,
        string $level = 'info'
    ): SymfonyResponse {
        $this->setLogger($logger);
        $this->level = $level;
        // $this->terminate($request, $next($request));

        return $next($request);
    }

    public function terminate(Request $request, SymfonyResponse $response): void
    {
        if ($this->shouldSkip($request)) {
            return;
        }

        $this->logger->log(
            $this->level,
            $this->messageFor($request, $response),
            $this->contextFor($request, $response)
        );
    }

    private function setLogger(null|LoggerInterface|string $logger): void
    {
        if (!$logger instanceof LoggerInterface) {
            $logger = Log::channel($logger);
        }

        if (!$logger instanceof Logger) {
            $logger = new Logger($logger, Event::getFacadeRoot());
        }

        $this->logger = $logger;
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

    private function contextFor(Request $request, SymfonyResponse $response): array
    {
        return [
            'method' => $request->method(),
            'path' => $request->path(),
            'request_header' => $this->headerFor($request),
            'query' => $request->query(),
            'post' => $this->postFor($request->post()),
            'files' => $this->filesFor($request),
            'status_code' => $response->getStatusCode(),
            'status_text' => $this->statusTextFor($response),
            'response_header' => $this->headerFor($response),
            'response_body' => $this->responseBodyFor($response),
            'ip' => $request->getClientIp(),
            'duration' => $duration = $this->duration(),
            'human_duration' => humans_milliseconds($duration, ['minimumUnit' => 'ms']),
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

    private function postFor(array $post): array
    {
        return collect($post)
            ->map(static fn (mixed $value, string $key): mixed => Str::is(self::$postHidden, $key) ? '***' : $value)
            ->all();
    }

    /**
     * @noinspection CallableParameterUseCaseInTypeContextInspection
     */
    private function filesFor(Request $request): array
    {
        $files = $request->allFiles();

        array_walk_recursive($files, static function (UploadedFile &$uploadedFile): void {
            $uploadedFile = [
                'name' => $uploadedFile->getClientOriginalName(),
                'type' => $uploadedFile->getMimeType(),
                'tmp_name' => $uploadedFile->getPathname(),
                // 'error' => $uploadedFile->getError(),
                'error' => $uploadedFile->getErrorMessage(),
                // 'size' => Utils::humanBytes($uploadedFile->getSize()),
            ];
        });

        return $files;
    }

    /**
     * @see \Symfony\Component\HttpFoundation\Response::setStatusCode()
     */
    private function statusTextFor(SymfonyResponse $response): string
    {
        return Response::$statusTexts[$response->getStatusCode()] ?? 'unknown status';
    }

    private function responseBodyFor(SymfonyResponse $response): string
    {
        $content = $response->getContent();

        if (json_validate($content)) {
            return $content;
        }

        return str(\sprintf('%s: %s', $response->headers->get('Content-Type'), $content))
            ->limit()
            ->toString();
    }

    /**
     * @return float milliseconds
     */
    private function duration(): float
    {
        return (microtime(true) - LARAVEL_START) * 1000;
    }
}
