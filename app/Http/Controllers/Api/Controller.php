<?php

namespace App\Http\Controllers\Api;

use App\Support\Attributes\Injection;
use F9Web\ApiResponseHelpers;
use Guanguans\LaravelApiResponse\Contracts\ApiResponseContract;
use Guanguans\LaravelApiResponse\Support\Traits\ApiResponseFactory;
use Jiannei\Response\Laravel\Response;

/**
 * @mixin \Jiannei\Response\Laravel\Response
 */
class Controller extends \App\Http\Controllers\Controller
{
    // use JsonResponseTrait;
    // use ApiResponseHelpers;
    use ApiResponseFactory;

    /**
     * @var \Guanguans\LaravelApiResponse\ApiResponse
     */
    #[Injection(ApiResponseContract::class)]
    protected ApiResponseContract $apiResponse;

    public function __call($name, $arguments)
    {
        if (method_exists(Response::class, $name)) {
            return app(Response::class)->{$name}(...$arguments);
        }

        return parent::__call($name, $arguments);
    }
}
