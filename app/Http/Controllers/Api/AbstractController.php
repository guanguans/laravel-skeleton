<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AbstractController as BaseController;
use App\Support\Attribute\Autowired;
use Guanguans\LaravelApiResponse\ApiResponse;
use Guanguans\LaravelApiResponse\Support\Traits\ApiResponseFactory;

abstract class AbstractController extends BaseController
{
    use ApiResponseFactory;

    #[Autowired(ApiResponse::class)]
    protected ApiResponse $apiResponse;
}
