<?php

namespace App\Policies;

use App\Models\JWTUser;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;

class Policy
{
    use HandlesAuthorization;

    /**
     * Retrieves all registered policies from the Gate. Only policies registered in the application
     * can be assigned to groups.
     */
    public static function all(): array
    {
        return array_keys(Gate::abilities());
    }

    public function before(JWTUser $user): ?bool
    {
        if ($user?->is_admin) {
            return true;
        }

        return null;
    }
}
