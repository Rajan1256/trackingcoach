<?php

namespace App\Policies;

use App\Enum\Roles;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  User  $user
     * @return Response|bool
     */
    public function viewAny(User $user)
    {
        if (!$user->hasCurrentTeamRole([Roles::ADMIN, Roles::COACH, Roles::PHYSIOLOGIST])) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  User  $model
     * @return Response|bool
     */
    public function view(User $user, User $model)
    {
        if ($user === $user) {
            return true;
        }
        
        // if the user doesn't belong to the current user's team, return false
        if (!$model->belongsToTeam(current_team())) {
            return false;
        }

        if ($user->hasCurrentTeamRole([Roles::CUSTOMER]) && $user->id === $model->id) {
            return true;
        }

        if ($user->hasCurrentTeamRole([Roles::COACH]) && $model->coach?->id === $user->id) {
            return true;
        }

        if ($user->hasCurrentTeamRole([Roles::PHYSIOLOGIST]) && $model->physiologist?->id === $user->id) {
            return true;
        }

        if ($user->hasCurrentTeamRole([Roles::ADMIN])) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return Response|bool
     */
    public function create(User $user)
    {
        if (current_team()->maxCustomers() < current_team()->users()->isCustomer()->count()) {
            return false;
        }

        if (!$user->hasCurrentTeamRole([Roles::ADMIN, Roles::COACH])) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  User  $model
     * @return Response|bool
     */
    public function update(User $user, User $model)
    {
        if ($user->hasCurrentTeamRole([Roles::CUSTOMER])) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  User  $model
     * @return Response|bool
     */
    public function delete(User $user, User $model)
    {
        if ($user->hasCurrentTeamRole([Roles::CUSTOMER])) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  User  $model
     * @return Response|bool
     */
    public function restore(User $user, User $model)
    {
        if ($user->hasCurrentTeamRole([Roles::CUSTOMER])) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @param  User  $model
     * @return Response|bool
     */
    public function forceDelete(User $user, User $model)
    {
        if ($user->hasCurrentTeamRole([Roles::CUSTOMER])) {
            return false;
        }

        return true;
    }
}
