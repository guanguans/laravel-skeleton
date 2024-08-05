<?php

namespace App\Http\Controllers\Api;

use App\Support\ApiResponse\ApiResponse;
use App\Support\Attributes\DependencyInjection;
use F9Web\ApiResponseHelpers;
use Jiannei\Response\Laravel\Response;

/**
 * @mixin \Jiannei\Response\Laravel\Response
 */
class Controller extends \App\Http\Controllers\Controller
{
    // use JsonResponseTrait;
    use ApiResponseHelpers;

    #[DependencyInjection(ApiResponse::class)]
    protected ApiResponse $apiResponse;

    public function __call($name, $arguments)
    {
        if (method_exists(Response::class, $name)) {
            return app(Response::class)->{$name}(...$arguments);
        }

        return parent::__call($name, $arguments);
    }
}
