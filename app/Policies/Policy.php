<?php

namespace App\Policies;

use App\Models\JWTUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class Policy
{
    use HandlesAuthorization;

    public function before(JWTUser $user)
    {
        if (optional($user)->is_admin) {
            return true;
        }
    }
}
