<?php

namespace App\Policies;

use App\Models\Admin;
use Illuminate\Auth\Access\Response;
use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Admin $admin)
    {
        return $admin->hasAnyPermission('add_user')? Response::allow(): Response::deny('Cannot Enter You Don\'t Have A Permission');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Admin $admin, User $model)
    {
        return $admin->hasAnyPermission('edit_user')? Response::allow(): Response::deny('Cannot Enter');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Admin $admin, User $model)
    {
        return $admin->hasAnyPermission('delete_user')? Response::allow(): Response::deny('Cannot Enter You Don\'t Have A Permission');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        //
    }
}
