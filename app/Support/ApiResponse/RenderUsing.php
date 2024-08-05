<?php

declare(strict_types=1);

namespace App\Support\ApiResponse;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RenderUsing
{
    /**
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    public function __invoke(\Throwable $throwable, Request $request): ?JsonResponse
    {
        if ($request->is('api/*')) {
            return app(ApiResponse::class)->throw($throwable);
        }
    }
}
