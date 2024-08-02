<?php

declare(strict_types=1);

namespace App\Support\Api\Pipes;

use App\Support\Api\Support\Utils;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Lang;
use Symfony\Component\HttpFoundation\Response;

class MessagePipe extends Pipe
{
    /**
     * @param  \Closure(array): \Illuminate\Http\JsonResponse  $next
     */
    public function handle(
        array $data,
        \Closure $next,
        string $key = 'http-statuses',
        string $default = 'Whoops, looks like something went wrong.',
    ): JsonResponse {
        $data['message'] = $data['message'] ?: $this->messageFor($data, $key, $default);

        return $next($data);
    }

    /**
     * @see \Symfony\Component\HttpFoundation\Response::setStatusCode()
     * @see \Illuminate\Foundation\Exceptions\Handler::prepareException()
     */
    private function messageFor(array $data, string $key, string $default): string
    {
        if (Lang::has($key = "$key.{$data['code']}")) {
            return __($key);
        }

        $statusCode = Utils::statusCodeFor($data['code']);
        if (Lang::has($key = "$key.$statusCode")) {
            return __($key);
        }

        // ['Server Error', 'Unknown Status'];
        return Response::$statusTexts[$statusCode] ?? $default;
    }
}
