<?php

namespace App\Events;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeleteTeamUser
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  User  $user
     * @param  Team  $team
     */
    public function __construct(public User $user, public Team $team)
    {
    }
}
