<?php

namespace App\Policies;

use App\Enum\Roles;
use App\Models\Test;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

use function current_team;

class TestPolicy
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
        if (!current_team()->isRoot()) {
            return false;
        }

        return $user->hasCurrentTeamRole([Roles::CUSTOMER, Roles::COACH, Roles::PHYSIOLOGIST, Roles::ADMIN]);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Test  $test
     * @return Response|bool
     */
    public function view(User $user, Test $test)
    {
        if (!current_team()->isRoot()) {
            return false;
        }

        return $user->hasCurrentTeamRole([Roles::CUSTOMER, Roles::COACH, Roles::PHYSIOLOGIST, Roles::ADMIN]);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return Response|bool
     */
    public function create(User $user)
    {
        if (!current_team()->isRoot()) {
            return false;
        }

        return $user->hasCurrentTeamRole([Roles::COACH, Roles::PHYSIOLOGIST, Roles::ADMIN]);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Test  $test
     * @return Response|bool
     */
    public function update(User $user, Test $test)
    {
        if (!current_team()->isRoot()) {
            return false;
        }

        return $user->hasCurrentTeamRole([Roles::COACH, Roles::PHYSIOLOGIST, Roles::ADMIN]);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Test  $test
     * @return Response|bool
     */
    public function delete(User $user, Test $test)
    {
        if (!current_team()->isRoot()) {
            return false;
        }

        return $user->hasCurrentTeamRole([Roles::COACH, Roles::PHYSIOLOGIST, Roles::ADMIN]);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  Test  $test
     * @return Response|bool
     */
    public function restore(User $user, Test $test)
    {
        if (!current_team()->isRoot()) {
            return false;
        }

        return $user->hasCurrentTeamRole([Roles::COACH, Roles::PHYSIOLOGIST, Roles::ADMIN]);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @param  Test  $test
     * @return Response|bool
     */
    public function forceDelete(User $user, Test $test)
    {
        if (!current_team()->isRoot()) {
            return false;
        }

        return $user->hasCurrentTeamRole([Roles::COACH, Roles::PHYSIOLOGIST, Roles::ADMIN]);
    }
}
