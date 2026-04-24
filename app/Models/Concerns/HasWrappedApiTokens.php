<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Models\Concerns;

use Jenssegers\Agent\Facades\Agent;
use Laravel\Sanctum\NewAccessToken;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 * @mixin \Laravel\Sanctum\HasApiTokens
 */
trait HasWrappedApiTokens
{
    /**
     * @return array<string, scalar>
     */
    public static function wrapToken(#[\SensitiveParameter] string $token): array
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('sanctum.expiration') * 60,
        ];
    }

    public function createWrappedPlainTextTokenWithoutName(array $abilities = ['*'], ?\DateTimeInterface $expiresAt = null): array
    {
        return self::wrapToken($this->createTokenWithoutName($abilities, $expiresAt)->plainTextToken);
    }

    public function createTokenWithoutName(array $abilities = ['*'], ?\DateTimeInterface $expiresAt = null): NewAccessToken
    {
        return $this->createToken(self::device(), $abilities, $expiresAt);
    }

    protected static function device(?string $userAgent = null): string
    {
        return Agent::device($userAgent) ?: 'unknown';
    }
}
