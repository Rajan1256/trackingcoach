<?php

namespace App\Policies;

use App\Models\Faq;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

use function current_team;

class FaqPolicy
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
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Faq  $faq
     * @return Response|bool
     */
    public function view(User $user, Faq $faq)
    {
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
        return current_team()->isRoot();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Faq  $faq
     * @return Response|bool
     */
    public function update(User $user, Faq $faq)
    {
        return current_team()->isRoot();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Faq  $faq
     * @return Response|bool
     */
    public function delete(User $user, Faq $faq)
    {
        return current_team()->isRoot();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  Faq  $faq
     * @return Response|bool
     */
    public function restore(User $user, Faq $faq)
    {
        return current_team()->isRoot();
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @param  Faq  $faq
     * @return Response|bool
     */
    public function forceDelete(User $user, Faq $faq)
    {
        return current_team()->isRoot();
    }
}
