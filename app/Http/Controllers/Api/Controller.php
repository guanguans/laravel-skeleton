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
use Guanguans\LaravelApiResponse\Contracts\ApiResponseContract;
use Guanguans\LaravelApiResponse\Support\Traits\ApiResponseFactory;

class Controller extends \App\Http\Controllers\Controller
{
    use ApiResponseFactory;

    #[Autowired(ApiResponseContract::class)]
    protected ApiResponseContract $apiResponse;
}
