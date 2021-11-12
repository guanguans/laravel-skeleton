<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\IndexRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Mail\UserRegisteredMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
     * @unauthenticated
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
        $credentials = $request->validateStrictAll([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (! $token = auth()->attempt($credentials)) {
            return $this->fail('邮箱或者密码错误');
        }

        // Mail::to($request->user())->send(new UserRegisteredMail());

        return $this->respondWithToken($token);
    }

    /**
     * me - 用户信息
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
        return $this->success(UserResource::make($request->user()));
    }

    /**
     * logout - 退出
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
     * index - 用户列表
     *
     * @unauthenticated
     * @queryParam per_page integer 分页大小. 默认值 15.
     * @queryParam page integer 第几页. 默认值 1.
     *
     * @response {
     *     "status": "success",
     *     "code": 200,
     *     "message": "Http ok",
     *     "data": {
     *         "data": [
     *             {
     *                 "id": 2,
     *                 "name": "Kenyatta Roberts",
     *                 "email": "wintheiser.laron@example.com",
     *                 "created_at": "2021-11-10T07:56:41.000000Z",
     *                 "updated_at": "2021-11-10T07:56:41.000000Z"
     *             }
     *         ],
     *         "meta": {
     *             "pagination": {
     *                 "total": 0,
     *                 "count": 2,
     *                 "per_page": "1",
     *                 "current_page": 2,
     *                 "total_pages": 0
     *             }
     *         }
     *     },
     *     "error": {}
     * }
     */
    public function index(IndexRequest $request)
    {
        // $validatedParameters = $request->validateStrictAll([
        //     'per_page' => 'integer|min:5|max:50',
        //     'page' => 'integer|min:1'
        // ]);

        $validatedParameters = $request->validated();

        $users = User::query()->simplePaginate($validatedParameters['per_page'] ?? null);

        return $this->success(UserCollection::make($users));
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
