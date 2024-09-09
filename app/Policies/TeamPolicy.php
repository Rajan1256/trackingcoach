<?php

namespace App\Policies;

use App\Enum\Roles;
use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

use function current_team;

class TeamPolicy
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
        if (current_team()->isRoot() && $user->hasCurrentTeamRole([Roles::ADMIN])) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Team  $team
     * @return Response|bool
     */
    public function view(User $user, Team $team)
    {
        if (current_team()->isRoot() && $user->hasCurrentTeamRole([Roles::ADMIN])) {
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
        if (current_team()->isRoot() && $user->hasCurrentTeamRole([Roles::ADMIN])) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Team  $team
     * @return Response|bool
     */
    public function update(User $user, Team $team)
    {
        if (current_team()->isRoot() && $user->hasCurrentTeamRole([Roles::ADMIN])) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Team  $team
     * @return Response|bool
     */
    public function delete(User $user, Team $team)
    {
        if (current_team()->isRoot() && $user->hasCurrentTeamRole([Roles::ADMIN])) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  Team  $team
     * @return Response|bool
     */
    public function restore(User $user, Team $team)
    {
        if (current_team()->isRoot() && $user->hasCurrentTeamRole([Roles::ADMIN])) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @param  Team  $team
     * @return Response|bool
     */
    public function forceDelete(User $user, Team $team)
    {
        if (current_team()->isRoot() && $user->hasCurrentTeamRole([Roles::ADMIN])) {
            return true;
        }

        return false;
    }
}
