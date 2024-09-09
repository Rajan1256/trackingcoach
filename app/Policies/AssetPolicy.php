<?php

namespace App\Policies;

use App\Enum\Roles;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class AssetPolicy
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
        if (!current_team()->hasPlanOption('storage_files')) {
            return false;
        }

        return $user->hasCurrentTeamRole([Roles::CUSTOMER, Roles::COACH, Roles::ADMIN]);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Asset  $asset
     * @return Response|bool
     */
    public function view(User $user, Asset $asset)
    {
        if (!current_team()->hasPlanOption('storage_files')) {
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
        if (!current_team()->hasPlanOption('storage_files')) {
            return false;
        }

        return $user->hasCurrentTeamRole([Roles::CUSTOMER, Roles::COACH, Roles::ADMIN]);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Asset  $asset
     * @return Response|bool
     */
    public function update(User $user, Asset $asset)
    {
        if (!current_team()->hasPlanOption('storage_files')) {
            return false;
        }

        return $user->hasCurrentTeamRole([Roles::CUSTOMER, Roles::COACH, Roles::ADMIN]);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Asset  $asset
     * @return Response|bool
     */
    public function delete(User $user, Asset $asset)
    {
        if (!current_team()->hasPlanOption('storage_files')) {
            return false;
        }

        if (!$user->hasCurrentTeamRole([Roles::CUSTOMER, Roles::COACH, Roles::ADMIN])) {
            return false;
        }

        return $asset->author_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  Asset  $asset
     * @return Response|bool
     */
    public function restore(User $user, Asset $asset)
    {
        if (!current_team()->hasPlanOption('storage_files')) {
            return false;
        }

        return $user->hasCurrentTeamRole([Roles::COACH, Roles::ADMIN]);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @param  Asset  $asset
     * @return Response|bool
     */
    public function forceDelete(User $user, Asset $asset)
    {
        if (!current_team()->hasPlanOption('storage_files')) {
            return false;
        }

        return $user->hasCurrentTeamRole([Roles::COACH, Roles::ADMIN]);
    }
}
