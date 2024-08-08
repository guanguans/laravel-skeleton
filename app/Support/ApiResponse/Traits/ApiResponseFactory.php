<?php

declare(strict_types=1);

namespace App\Support\ApiResponse\Traits;

use App\Support\ApiResponse\ApiResponse;

trait ApiResponseFactory
{
    public function apiResponse(): ApiResponse
    {
        return app(ApiResponse::class);
    }
}
