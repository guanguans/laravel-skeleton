<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

/**
 * @group Ping - 示例接口管理
 */
class PingController extends Controller
{
    /**
     * 示例接口
     *
     * @header Content-Type application/json
     * @urlParam id integer required
     * @queryParam is_bad integer 错误请求示例. 默认值 0.
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
    public function ping(Request $request)
    {
        if ($request->input('is_bad')) {
            return $this->errorBadRequest('This is a bad example.');
        }


        return $this->ok('This is a successful example.');
    }
}
