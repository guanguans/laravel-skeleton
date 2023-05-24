<?php

namespace App\Http\Controllers\Api;

use F9Web\ApiResponseHelpers;
use Jiannei\Response\Laravel\Response;
use Jiannei\Response\Laravel\Support\Traits\JsonResponseTrait;

/**
 * @mixin \Jiannei\Response\Laravel\Response
 */
class Controller extends \App\Http\Controllers\Controller
{
    // use JsonResponseTrait;
    use ApiResponseHelpers;

    public function __call($name, $arguments)
    {
        if (method_exists(Response::class, $name)) {
            return app(Response::class)->{$name}(...$arguments);
        }

        return parent::__call($name, $arguments);
    }
}
