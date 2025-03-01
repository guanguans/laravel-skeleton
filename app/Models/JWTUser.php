<?php

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
            /** @var \Illuminate\Auth\AuthManager|\PHPOpenSourceSaver\JWTAuth\JWTGuard|\PHPOpenSourceSaver\JWTAuth\JWT */
            'expires_in' => auth()->factory()->getTTL() * 60,
        ];
    }
}
