<?php

namespace App\Traits;

trait HasWrapedApiTokens
{
    /**
     * @param  string  $token
     *
     * @return array
     */
    public static function wrapToken(string $token): array
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('sanctum.expiration') * 60
        ];
    }

    public static function getDevice(?string $userAgent = null): string
    {
        return \Jenssegers\Agent\Facades\Agent::device($userAgent) ?: 'unknown';
    }

    /**
     * @param  array  $abilities
     *
     * @return \Laravel\Sanctum\NewAccessToken
     */
    public function createTokenWithoutName(array $abilities = ['*'])
    {
        /* @var \App\Models\User|\App\Models\JWTUser $this */
        return $this->createToken(self::getDevice(), $abilities);
    }

    public function createWrapedPlainTextTokenWithoutName(array $abilities = ['*'])
    {
        return self::wrapToken($this->createTokenWithoutName($abilities)->plainTextToken);
    }
}
