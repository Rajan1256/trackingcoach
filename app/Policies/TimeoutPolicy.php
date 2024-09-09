<?php

namespace App\Policies;

use App\Enum\Roles;
use App\Models\Timeout;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TimeoutPolicy
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
        return $user->hasCurrentTeamRole([Roles::CUSTOMER, Roles::COACH, Roles::ADMIN]);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Timeout  $timeout
     * @return Response|bool
     */
    public function view(User $user, Timeout $timeout)
    {
        return $user->hasCurrentTeamRole([Roles::CUSTOMER, Roles::COACH, Roles::ADMIN]);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return Response|bool
     */
    public function create(User $user)
    {
        return $user->hasCurrentTeamRole([Roles::CUSTOMER, Roles::ADMIN]);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Timeout  $timeout
     * @return Response|bool
     */
    public function update(User $user, Timeout $timeout)
    {
        return $user->hasCurrentTeamRole([Roles::CUSTOMER, Roles::ADMIN]);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Timeout  $timeout
     * @return Response|bool
     */
    public function delete(User $user, Timeout $timeout)
    {
        return $user->hasCurrentTeamRole([Roles::CUSTOMER, Roles::ADMIN]);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  Timeout  $timeout
     * @return Response|bool
     */
    public function restore(User $user, Timeout $timeout)
    {
        return $user->hasCurrentTeamRole([Roles::CUSTOMER, Roles::ADMIN]);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @param  Timeout  $timeout
     * @return Response|bool
     */
    public function forceDelete(User $user, Timeout $timeout)
    {
        return $user->hasCurrentTeamRole([Roles::CUSTOMER, Roles::ADMIN]);
    }
}
