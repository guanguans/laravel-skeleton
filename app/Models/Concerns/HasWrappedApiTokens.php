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

namespace App\Models\Concerns;

use Jenssegers\Agent\Facades\Agent;
use Laravel\Sanctum\NewAccessToken;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 * @mixin \Laravel\Sanctum\HasApiTokens
 */
trait HasWrappedApiTokens
{
    public static function wrapToken(#[\SensitiveParameter] string $token): array
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('sanctum.expiration') * 60,
        ];
    }

    public static function getDevice(?string $userAgent = null): string
    {
        return Agent::device($userAgent) ?: 'unknown';
    }

    public function createTokenWithoutName(array $abilities = ['*']): NewAccessToken
    {
        return $this->createToken(self::getDevice(), $abilities);
    }

    public function createWrappedPlainTextTokenWithoutName(array $abilities = ['*']): array
    {
        return self::wrapToken($this->createTokenWithoutName($abilities)->plainTextToken);
    }
}
