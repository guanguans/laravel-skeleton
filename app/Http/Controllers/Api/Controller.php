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
