<?php

namespace App\Policies;

use App\Enum\Roles;
use App\Models\Interview;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

use function current_team;

class InterviewPolicy
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
        if (!current_team()->hasPlanOption('survey_and_interview')) {
            return false;
        }

        return $user->hasCurrentTeamRole([Roles::CUSTOMER, Roles::COACH, Roles::ADMIN]);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Interview  $interview
     * @return Response|bool
     */
    public function view(User $user, Interview $interview)
    {
        if (!current_team()->hasPlanOption('survey_and_interview')) {
            return false;
        }

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
        if (!current_team()->hasPlanOption('survey_and_interview')) {
            return false;
        }

        return $user->hasCurrentTeamRole([Roles::COACH, Roles::ADMIN]);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Interview  $interview
     * @return Response|bool
     */
    public function update(User $user, Interview $interview)
    {
        if (!current_team()->hasPlanOption('survey_and_interview')) {
            return false;
        }

        return $user->hasCurrentTeamRole([Roles::COACH, Roles::ADMIN]);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Interview  $interview
     * @return Response|bool
     */
    public function delete(User $user, Interview $interview)
    {
        if (!current_team()->hasPlanOption('survey_and_interview')) {
            return false;
        }

        return $user->hasCurrentTeamRole([Roles::COACH, Roles::ADMIN]);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  Interview  $interview
     * @return Response|bool
     */
    public function restore(User $user, Interview $interview)
    {
        if (!current_team()->hasPlanOption('survey_and_interview')) {
            return false;
        }

        return $user->hasCurrentTeamRole([Roles::COACH, Roles::ADMIN]);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @param  Interview  $interview
     * @return Response|bool
     */
    public function forceDelete(User $user, Interview $interview)
    {
        if (!current_team()->hasPlanOption('survey_and_interview')) {
            return false;
        }

        return $user->hasCurrentTeamRole([Roles::COACH, Roles::ADMIN]);
    }
}
