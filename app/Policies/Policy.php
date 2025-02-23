<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

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
