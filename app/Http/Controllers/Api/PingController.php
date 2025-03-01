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
     * ping - 示例接口
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
    public function ping(Request $request, mixed $isBad = 0): JsonResponse
    {
        $validatedParameters = $request->validateStrictAll([
            'is_bad' => 'integer',
        ]);

        if (($validatedParameters['is_bad'] ?? 0) || $isBad) {
            $this->errorBadRequest('This is a bad example.');
        }

        return $this->apiResponse->ok('This is a successful example.');
    }
}
