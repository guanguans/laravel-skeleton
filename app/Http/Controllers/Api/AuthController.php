<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\AuthRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Mail\UserRegisteredMail;
use App\Models\JWTUser;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use Faker\Generator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @group Auth - 认证接口管理
 */
class AuthController extends Controller
{
    public function __construct() {}

    /**
     * register - 注册
     *
     * @unauthenticated
     *
     * @bodyParam email string 邮箱。
     * @bodyParam password string 密码。
     * @bodyParam password_confirmation string 重复密码。
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
    public function register(AuthRequest $request)
    {
        // $validated = $request->validateStrictAll([
        //     'email' => 'required|email|unique:App\Models\JWTUser,email',
        //     'password' => 'required|string|min:8|confirmed',
        //     'password_confirmation' => 'required|same:password',
        // ]);
        $validated = $request->validated();

        $validated['name'] = app(Generator::class)->name;
        $validated['password'] = Hash::make($validated['password']);
        unset($validated['password_confirmation']);
        $user = JWTUser::query()->create($validated);
        if (! $user instanceof JWTUser) {
            return $this->fail('创建用户失败');
        }

        $validated['password'] = $request->post('password_confirmation');
        if (! $token = auth()->attempt($validated)) {
            return $this->fail('邮箱或者密码错误');
        }

        return tap($this->success(JWTUser::wrapToken($token)), static function ($response) use ($user): void {
            // Mail::to($user)->queue(new UserRegisteredMail());
            $user->notify((new WelcomeNotification)->delay(now()->addSeconds(60)));
        });
    }

    /**
     * login - 登录
     *
     * @unauthenticated
     *
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
    public function login(AuthRequest $request): \Illuminate\Http\JsonResponse
    {
        // $credentials = $request->validateStrictAll([
        //     'email' => 'required|email',
        //     'password' => 'required|string',
        // ]);
        $credentials = $request->validated();

        if (! $token = auth()->attempt($credentials)) {
            return $this->fail('邮箱或者密码错误');
        }

        return $this->success(JWTUser::wrapToken($token));
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
    public function me(Request $request): \Illuminate\Http\JsonResponse
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
        return tap($this->ok('退出成功'), function ($response): void {
            $this->authorize('update', auth()->user());
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
    public function refresh(Request $request): \Illuminate\Http\JsonResponse
    {
        if ($request->user()->cant('update', $request->user())) {
            return $this->errorForbidden();
        }

        return $this->success(JWTUser::wrapToken(auth()->refresh()));
    }

    /**
     * index - 用户列表
     *
     * @unauthenticated
     *
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
    public function index(AuthRequest $request): \Illuminate\Http\JsonResponse
    {
        $validatedParameters = $request->validated();

        $users = User::query()->simplePaginate($validatedParameters['per_page'] ?? null);

        return $this->success(UserCollection::make($users));
    }
}
