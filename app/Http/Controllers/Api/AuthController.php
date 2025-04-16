<?php

/** @noinspection PhpUnusedAliasInspection */

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

use App\Http\Requests\Auth\IndexRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Mail\UserRegisteredMail;
use App\Models\JWTUser;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Timebox;
use function Illuminate\Support\defer;

/**
 * @see https://laravel-jwt-auth.readthedocs.io/en/latest/quick-start/
 * @see https://github.com/nandi95/laravel-starter/blob/main/app/Http/Controllers/Authentication/
 */
class AuthController extends Controller
{
    public function index(IndexRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $users = User::query()->simplePaginate($validated['per_page'] ?? null)->withQueryString();

        return $this->apiResponse()->success(UserCollection::make($users));
    }

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            // 'name' => \sprintf('nullable|string|between:2,16|default:%s', fake()->name()),
            'email' => 'required|email|unique:App\Models\JWTUser,email',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|same:password',
        ]);

        $validated['name'] = fake()->name();

        JWTUser::query()->create(Arr::except($validated, 'password_confirmation'));

        /** @var string $token */
        $token = auth()->attempt($validated);

        return tap(
            $this->apiResponse()->success(JWTUser::wrapToken($token)),
            static function (): void {
                defer(static function (): void {
                    // Mail::to($user)->queue(new UserRegisteredMail);
                    // $user->notify((new WelcomeNotification)->delay(now()->addSeconds(60)));
                });
            }
        );
    }

    /**
     * @throws \Throwable
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        /**
         * @see https://securinglaravel.com/security-tip-timebox-for-timing-attacks
         */
        $token = (new Timebox)->call(
            static function (Timebox $timebox) use ($credentials) {
                $token = auth()->attempt($credentials);

                if ($token) {
                    $timebox->returnEarly();
                }

                return $token;
            },
            100 * 1000
        );

        if (!$token) {
            return $this->apiResponse()->badRequest('邮箱或者密码错误');
        }

        return tap(
            $this->apiResponse()->success(JWTUser::wrapToken($token)),
            static function (): void {
                // auth('web')->logoutOtherDevices(auth()->user()->password);
            }
        );
    }

    public function me(Request $request): JsonResponse
    {
        return $this->apiResponse()->success(UserResource::make($request->user()));
    }

    public function refresh(Request $request): JsonResponse
    {
        if ($request->user()->cant('update', $request->user())) {
            return $this->apiResponse()->forbidden();
        }

        /** @noinspection PhpParamsInspection */
        return $this->apiResponse()->success(JWTUser::wrapToken(auth()->refresh()));
    }

    public function logout(): JsonResponse
    {
        return tap($this->apiResponse()->ok('退出成功'), function (): void {
            $this->authorize('update', auth()->user());
            // auth()->user()->currentAccessToken()->delete();
            // auth()->logoutCurrentDevice();
            auth()->logout();
        });
    }
}
