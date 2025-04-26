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

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Overtrue\LaravelUploader\StrategyResolver;

final class UploadController extends Controller
{
    public function __invoke(string $strategy, Request $request): JsonResponse
    {
        $response = StrategyResolver::resolveFromRequest($request, $strategy)->upload()->toArray();
        // unset($response['url'], $response['disk'], $response['location']);

        return $this->apiResponse()->success($response);
    }
}
