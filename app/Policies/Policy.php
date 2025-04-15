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
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }
}
