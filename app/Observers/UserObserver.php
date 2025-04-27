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

namespace App\Observers;

use App\Models\User;

final class UserObserver
{
    public function creating(User $user): void {}

    public function created(User $user): void {}

    public function updating(User $user): void {}

    public function updated(User $user): void {}

    public function saving(User $user): void {}

    public function saved(User $user): void {}

    public function deleting(User $user): void {}

    public function deleted(User $user): void {}

    public function restoring(User $user): void {}

    public function restored(User $user): void {}

    public function retrieved(User $user): void {}

    public function forceDeleting(User $user): void {}

    public function forceDeleted(User $user): void {}

    public function replicating(User $user): void {}
}
