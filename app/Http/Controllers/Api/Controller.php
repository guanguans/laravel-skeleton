<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Http\Controllers\Api;

use App\Support\Attributes\Autowired;
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

    #[Autowired(ApiResponseContract::class)]
    protected ApiResponseContract $apiResponse;

    #[\Override]
    public function __call($name, $arguments)
    {
        if (method_exists(Response::class, $name)) {
            return app(Response::class)->{$name}(...$arguments);
        }

        return parent::__call($name, $arguments);
    }
}
