<?php

namespace App\Policies;

use App\Models\JWTUser;
use App\Models\User;

class UserPolicy extends Policy
{
    /**
     * Determine whether the user can view any models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(JWTUser $jWTUser)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(JWTUser $jWTUser, User $model)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(JWTUser $jWTUser)
    {
        return $jWTUser->can('create-user');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(JWTUser $jWTUser, User $model)
    {
        return $jWTUser->id == $model->id || $jWTUser->can('update-user');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(JWTUser $jWTUser, User $model)
    {
        return $jWTUser->id == $model->id || $jWTUser->can('delete-user');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(JWTUser $jWTUser, User $model)
    {
        return $jWTUser->id == $model->id || $jWTUser->can('restore-user');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(JWTUser $jWTUser, User $model)
    {
        return $jWTUser->id == $model->id || $jWTUser->can('delete-user');
    }
}
