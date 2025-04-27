<?php

/** @noinspection ClassOverridesFieldOfSuperClassInspection */

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

final class JWTUser extends User implements JWTSubject
{
    protected $table = 'users';

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * @see \PHPOpenSourceSaver\JWTAuth\JWTGuard::payload()
     */
    public function getJWTCustomClaims(): array
    {
        return $this->only(['id', 'name']);
    }

    /**
     * @see https://laravel-jwt-auth.readthedocs.io/en/latest/quick-start/
     */
    public static function wrapToken(#[\SensitiveParameter] string $token): array
    {
        /** @noinspection PhpParamsInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ];
    }
}
