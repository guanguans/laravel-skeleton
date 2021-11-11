<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

/**
 * @group Auth - 认证接口管理
 */
class AuthController extends Controller
{
    public function __construct()
    {
    }

    /**
     * login - 登录
     *
     * @header Content-Type application/json
     * @bodyParam email string 邮箱。
     * @bodyParam password string 密码。
     *
     * @response {
     *     "status": "success",
     *     "code": 200,
     *     "message": "Http ok",
     *     "data": {
     *         "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
     *         "token_type": "bearer",
     *         "expires_in": 3600
     *     },
     *     "error": {}
     * }
     */
    public function login(Request $request)
    {
        $this->validateData(
            $credentials = [
                'email' => $request->post('email'),
                'password' => $request->post('password'),
            ],
            [
                'email' => 'required|email',
                'password' => 'required|string',
            ]
        );

        if (! $token = auth()->attempt($credentials)) {
            return $this->fail('邮箱或者密码错误');
        }

        return $this->respondWithToken($token);
    }

    /**
     * me - 用户信息
     *
     * @header Content-Type application/json
     * @header Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
     *
     * @response {
     *     "status": "success",
     *     "code": 200,
     *     "message": "Http ok",
     *     "data": {
     *         "id": 1,
     *         "name": "admin",
     *         "email": "admin@admin.com",
     *         "email_verified_at": "2021-11-10T07:56:41.000000Z",
     *         "created_at": "2021-11-10T07:56:41.000000Z",
     *         "updated_at": "2021-11-10T07:56:41.000000Z"
     *     },
     *     "error": {}
     * }
     */
    public function me(Request $request)
    {
        return $this->success($request->user());
    }

    /**
     * logout - 退出
     *
     * @header Content-Type application/json
     * @header Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
     *
     * @response {
     *     "status": "success",
     *     "code": 200,
     *     "message": "退出成功",
     *     "data": {},
     *     "error": {}
     * }
     */
    public function logout()
    {
        return tap($this->ok('退出成功'), function ($response) {
            auth()->logout();
        });
    }

    /**
     * refresh - 重刷 token
     *
     * @header Content-Type application/json
     * @header Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
     *
     * @response {
     *     "status": "success",
     *     "code": 200,
     *     "message": "Http ok",
     *     "data": {
     *         "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
     *         "token_type": "bearer",
     *         "expires_in": 3600
     *     },
     *     "error": {}
     * }
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * @param $token
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    protected function respondWithToken($token)
    {
        return $this->success([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
