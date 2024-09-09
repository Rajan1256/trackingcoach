<?php

namespace App\Policies;

use App\Enum\Roles;
use App\Models\Note;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class NotePolicy
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
        if (!current_team()->hasPlanOption('storage_notes')) {
            return false;
        }

        return $user->hasCurrentTeamRole([Roles::CUSTOMER, Roles::COACH, Roles::ADMIN]);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Note  $note
     * @return Response|bool
     */
    public function view(User $user, Note $note)
    {
        if (!current_team()->hasPlanOption('storage_notes')) {
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
        if (!current_team()->hasPlanOption('storage_notes')) {
            return false;
        }

        return $user->hasCurrentTeamRole([Roles::CUSTOMER, Roles::COACH, Roles::ADMIN]);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Note  $note
     * @return Response|bool
     */
    public function update(User $user, Note $note)
    {
        if (!current_team()->hasPlanOption('storage_notes')) {
            return false;
        }

        if ($note->author_id !== $user->id) {
            return false;
        }

        return $user->hasCurrentTeamRole([Roles::CUSTOMER, Roles::COACH, Roles::ADMIN]);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Note  $note
     * @return Response|bool
     */
    public function delete(User $user, Note $note)
    {
        if (!current_team()->hasPlanOption('storage_notes')) {
            return false;
        }

        if ($user->hasCurrentTeamRole([Roles::ADMIN])) {
            return true;
        }

        if ($note->author_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  Note  $note
     * @return Response|bool
     */
    public function restore(User $user, Note $note)
    {
        if (!current_team()->hasPlanOption('storage_notes')) {
            return false;
        }

        return $user->hasCurrentTeamRole([Roles::COACH, Roles::ADMIN]);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @param  Note  $note
     * @return Response|bool
     */
    public function forceDelete(User $user, Note $note)
    {
        if (!current_team()->hasPlanOption('storage_notes')) {
            return false;
        }

        return $user->hasCurrentTeamRole([Roles::COACH, Roles::ADMIN]);
    }
}
