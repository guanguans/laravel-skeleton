<?php

declare(strict_types=1);

namespace App\Support\ApiResponse\Pipes;

use App\Support\ApiResponse\Pipes\Concerns\WithArgs;
use App\Support\ApiResponse\Support\Utils;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class MessagePipe
{
    use WithArgs;

    /**
     * @param  array{
     *  status: string,
     *  code: int,
     *  message: string,
     *  data: mixed,
     *  error: ?array,
     * }  $data
     * @param  \Closure(array): \Illuminate\Http\JsonResponse  $next
     * @param  string  $default  // ['Whoops, looks like something went wrong.', 'Server Error', 'Unknown Status']
     */
    public function handle(
        array $data,
        \Closure $next,
        string $mainKey = 'http-statuses',
        string $default = 'Whoops, looks like something went wrong.',
    ): JsonResponse {
        $data['message'] = __($data['message'] ?: $this->keyFor($data['code'], $mainKey, $default));

        return $next($data);
    }

    /**
     * @see \Illuminate\Foundation\Exceptions\Handler::prepareException()
     * @see \Illuminate\Foundation\Exceptions\Handler::convertExceptionToArray()
     * @see \Symfony\Component\HttpFoundation\Response::setStatusCode()
     */
    private function keyFor(int $code, string $mainKey, string $default): string
    {
        /** @var \Illuminate\Translation\Translator $translator */
        $translator = trans();

        if ($translator->has($key = "$mainKey.$code")) {
            return $key;
        }

        $statusCode = Utils::statusCodeFor($code);
        if ($translator->has($key = "$mainKey.$statusCode")) {
            return $key;
        }

        return Response::$statusTexts[$statusCode] ?? $default;
    }
}
