<?php

namespace App\Policies;

use App\Enum\Roles;
use App\Models\Review;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ReviewPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return Response|bool
     */
    public function create(User $user)
    {
        return $user->hasCurrentTeamRole([Roles::COACH, Roles::ADMIN]);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Review  $review
     * @return Response|bool
     */
    public function delete(User $user, Review $review)
    {
        return $user->hasCurrentTeamRole([Roles::COACH, Roles::ADMIN]);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @param  Review  $review
     * @return Response|bool
     */
    public function forceDelete(User $user, Review $review)
    {
        return $user->hasCurrentTeamRole([Roles::COACH, Roles::ADMIN]);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  Review  $review
     * @return Response|bool
     */
    public function restore(User $user, Review $review)
    {
        return $user->hasCurrentTeamRole([Roles::COACH, Roles::ADMIN]);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Review  $review
     * @return Response|bool
     */
    public function update(User $user, Review $review)
    {
        return $user->hasCurrentTeamRole([Roles::COACH, Roles::ADMIN]);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Review  $review
     * @return Response|bool
     */
    public function view(User $user, Review $review)
    {
        if ($user->hasCurrentTeamRole([Roles::COACH, Roles::ADMIN])) {
            return true;
        }
        
        if ($user->hasCurrentTeamRole([Roles::CUSTOMER]) && ($review->visible_at->isPast() || $review->visible_at->isToday())) {
            return true;
        }

        return false;
    }

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
}
