<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Jiannei\Response\Laravel\Response;

/**
 * @method JsonResponse|JsonResource accepted($data = null, string $message = '', string $location = '')
 * @method JsonResponse|JsonResource created($data = null, string $message = '', string $location = '')
 * @method JsonResponse|JsonResource noContent(string $message = '')
 * @method JsonResponse|JsonResource localize(int $code = 200, array $headers = [], int $option = 0)
 * @method JsonResponse|JsonResource ok(string $message = '', int $code = 200, array $headers = [], int $option = 0)
 * @method JsonResponse|JsonResource success($data = null, string $message = '', int $code = 200, array $headers = [], int $option = 0)
 * @method JsonResponse|JsonResource errorBadRequest(?string $message = '')
 * @method JsonResponse|JsonResource errorUnauthorized(string $message = '')
 * @method JsonResponse|JsonResource errorForbidden(string $message = '')
 * @method JsonResponse|JsonResource errorNotFound(string $message = '')
 * @method JsonResponse|JsonResource errorMethodNotAllowed(string $message = '')
 * @method JsonResponse|JsonResource errorInternal(string $message = '')
 * @method JsonResponse|JsonResource fail(string $message = '', int $code = 500, $errors = null, array $header = [], int $options = 0)
 *
 * @see \Jiannei\Response\Laravel\Response
 */
class Controller extends \App\Http\Controllers\Controller
{
    public function __call($name, $arguments)
    {
        return app(Response::class)->{$name}(...$arguments);
    }
}
