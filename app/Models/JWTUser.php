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

namespace App\Models;

use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Watson\Validating\ValidatingTrait;

class JWTUser extends User implements JWTSubject
{
    use ValidatingTrait;
    protected $table = 'users';
    protected $rules = [
        // 'email' => 'required|email',
        // 'password' => 'required|string',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    #[\Override]
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    #[\Override]
    public function getJWTCustomClaims()
    {
        return [];
    }

    public static function wrapToken(string $token): array
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            /** @var \Illuminate\Auth\AuthManager|\PHPOpenSourceSaver\JWTAuth\JWT|\PHPOpenSourceSaver\JWTAuth\JWTGuard */
            'expires_in' => auth()->factory()->getTTL() * 60,
        ];
    }
}
