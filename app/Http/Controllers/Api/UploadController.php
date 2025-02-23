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

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Overtrue\LaravelUploader\StrategyResolver;

class UploadController extends Controller
{
    public function __invoke(string $strategy, Request $request): JsonResponse
    {
        $response = StrategyResolver::resolveFromRequest($request, $strategy)->upload()->toArray();
        // unset($response['url'], $response['disk'], $response['location']);

        return $this->apiResponse->success($response);
    }
}
