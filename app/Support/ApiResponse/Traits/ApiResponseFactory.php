<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\ApiResponse\Traits;

use App\Support\ApiResponse\ApiResponse;

trait ApiResponseFactory
{
    public function apiResponse(): ApiResponse
    {
        return app(ApiResponse::class);
    }
}
