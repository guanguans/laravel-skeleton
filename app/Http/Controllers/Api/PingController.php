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

/**
 * @group Ping - 示例接口管理
 */
class PingController extends Controller
{
    /**
     * @see \Illuminate\Routing\Controllers\HasMiddleware
     */
    #[\Override]
    public function middleware(mixed $middleware, array $options = [])
    {
        return parent::middleware($middleware, $options);
    }

    /**
     * ping - 示例接口.
     *
     * @unauthenticated
     *
     * @urlParam is_bad integer 错误请求示例. 默认值 0.
     *
     * @queryParam is_bad integer 错误请求示例. 默认值 0.
     *
     * @bodyParam is_bad integer 错误请求示例. 默认值 0.
     *
     * @response {
     *     "status": "success",
     *     "code": 200,
     *     "message": "This is a successful example.",
     *     "data": {},
     *     "error": {}
     * }
     */
    public function ping(Request $request, int $isBad = 0): JsonResponse
    {
        $validatedParameters = $request->validateStrictAll([
            'is_bad' => 'integer',
        ]);

        if (($validatedParameters['is_bad'] ?? 0) || $isBad) {
            return $this->apiResponse()->badRequest('This is a bad example.');
        }

        return $this->apiResponse()->ok('This is a successful example.');
    }
}
