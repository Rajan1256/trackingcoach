<?php

namespace App\Policies;

use App\Enum\Roles;
use App\Models\ProgramMilestone;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ProgramMilestonePolicy
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
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  ProgramMilestone  $programMilestone
     * @return Response|bool
     */
    public function view(User $user, ProgramMilestone $programMilestone)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return Response|bool
     */
    public function create(User $user)
    {
        if ($user->hasCurrentTeamRole([Roles::COACH, Roles::ADMIN])) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  ProgramMilestone  $programMilestone
     * @return Response|bool
     */
    public function update(User $user, ProgramMilestone $programMilestone)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  ProgramMilestone  $programMilestone
     * @return Response|bool
     */
    public function delete(User $user, ProgramMilestone $programMilestone)
    {
        if ($user->hasCurrentTeamRole([Roles::COACH, Roles::ADMIN])) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  ProgramMilestone  $programMilestone
     * @return Response|bool
     */
    public function restore(User $user, ProgramMilestone $programMilestone)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @param  ProgramMilestone  $programMilestone
     * @return Response|bool
     */
    public function forceDelete(User $user, ProgramMilestone $programMilestone)
    {
        //
    }
}
