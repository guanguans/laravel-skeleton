<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Models\Concerns;

use Jenssegers\Agent\Facades\Agent;
use Laravel\Sanctum\NewAccessToken;

/**
 * @mixin \App\Models\User
 */
trait HasWrapedApiTokens
{
    public static function wrapToken(string $token): array
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

    public function createWrapedPlainTextTokenWithoutName(array $abilities = ['*']): array
    {
        return self::wrapToken($this->createTokenWithoutName($abilities)->plainTextToken);
    }
}
