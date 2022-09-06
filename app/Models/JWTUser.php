<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
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
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

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
            /** @var \Illuminate\Auth\AuthManager|\Tymon\JWTAuth\JWTGuard|\Tymon\JWTAuth\JWT */
            'expires_in' => auth()->factory()->getTTL() * 60
        ];
    }
}
