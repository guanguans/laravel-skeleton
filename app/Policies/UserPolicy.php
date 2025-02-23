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
use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy extends Policy
{
    /**
     * Determine whether the user can view any models.
     *
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function viewAny(JWTUser $jWTUser): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function view(JWTUser $jWTUser, User $model): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(JWTUser $jWTUser): bool|Response
    {
        return $jWTUser->can('create-user');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function update(JWTUser $jWTUser, User $model): bool
    {
        return $jWTUser->id === $model->id || $jWTUser->can('update-user');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function delete(JWTUser $jWTUser, User $model): bool
    {
        return $jWTUser->id === $model->id || $jWTUser->can('delete-user');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function restore(JWTUser $jWTUser, User $model): bool
    {
        return $jWTUser->id === $model->id || $jWTUser->can('restore-user');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function forceDelete(JWTUser $jWTUser, User $model): bool
    {
        return $jWTUser->id === $model->id || $jWTUser->can('delete-user');
    }
}
