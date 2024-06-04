<?php

namespace App\Http\Middleware;

use App\Models\HttpLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class LogHttp
{
    private array $exceptMethods = [];

    private array $exceptPaths = [];

    private array $removedHeaders = [
        'Authorization',
        'Cookie',
    ];

    private array $removedInputs = [
        'password',
        'password_confirmation',
        'new_password',
        'old_password',
        'password',
        '*password',
        'password*',
        '*password*',
    ];

    private static array $skipCallbacks = [];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // $this->logHttp($request, $next($request));

        return $next($request);
    }

    /**
     * @param  \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse  $response
     */
    public function terminate(Request $request, $response): void
    {
        $this->logHttp($request, $response);
    }

    public static function skipWhen(Closure $callback): void
    {
        static::$skipCallbacks[] = $callback;
    }

    /**
     * @param  \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse  $response
     */
    protected function logHttp(Request $request, $response): void
    {
        if ($this->shouldLogHttp($request)) {
            HttpLog::query()->create($this->collectData($request, $response));
        }
    }

    protected function shouldLogHttp(Request $request): bool
    {
        return ! $this->shouldntLogHttp($request);
    }

    protected function shouldntLogHttp(Request $request): bool
    {
        if (\in_array($request->method(), array_map('strtoupper', $this->exceptMethods), true)) {
            return true;
        }

        foreach ($this->exceptPaths as $exceptPath) {
            $exceptPath === '/' or $exceptPath = trim((string) $exceptPath, '/');
            if ($request->fullUrlIs($exceptPath) || $request->is($exceptPath)) {
                return true;
            }
        }

        return $this->shouldSkip($request);
    }

    protected function shouldSkip(Request $request): bool
    {
        foreach (static::$skipCallbacks as $callback) {
            if ($callback($request)) {
                return true;
            }
        }

        return false;
    }

    /** @noinspection PhpUnused */
    protected function shouldntSkip(Request $request): bool
    {
        return ! $this->shouldSkip($request);
    }

    /**
     * @param  \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse  $response
     */
    protected function collectData(Request $request, $response): array
    {
        return [
            'method' => substr($request->method(), 0, 10),
            'path' => substr($request->path(), 0, 128),
            'request_header' => $this->getMaxLengthFor($this->extractHeader($request)),
            'input' => $this->getMaxLengthFor($this->extractInput($request)),
            'response_header' => $this->getMaxLengthFor($this->extractHeader($response)),
            'response' => $this->getMaxLengthFor((string) ($response->getContent())),
            'ip' => substr((string) $request->getClientIp(), 0, 16),
            'duration' => substr($this->calculateDuration(), 0, 10),
        ];
    }

    protected function getMaxLengthFor(string $content): string
    {
        // MySQL text 类型最大 64KB (65535)
        return substr($content, 0, 60 * 1024);
    }

    /**
     * @param  \Illuminate\Http\Request|\Illuminate\Http\Response  $requestOrResponse
     * @param  \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse  $requestOrResponse
     */
    protected function extractHeader($requestOrResponse): string
    {
        $header = Arr::except(
            $requestOrResponse->headers->all(),
            array_map('strtolower', $this->removedHeaders)
        );

        /** @noinspection JsonEncodingApiUsageInspection */
        return (string) json_encode($header);
    }

    protected function extractInput(Request $request): string
    {
        /** @noinspection JsonEncodingApiUsageInspection */
        return (string) json_encode($request->except($this->removedInputs));
    }

    protected function calculateDuration(): string
    {
        return number_format(microtime(true) - LARAVEL_START, 3);
    }
}
