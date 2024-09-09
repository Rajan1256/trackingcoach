<?php

namespace App\Modules\Team\Http\Middleware;

use App\Models\Team;
use App\Modules\Team\Exceptions\NoMembershipForThisTeamException;
use Auth;
use Closure;

class HasMembershipForTeam
{
    /**
     * @throws NoMembershipForThisTeamException
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::user()->belongsToTeam(Team::current())) {
            $this->handleInvalidRequest();
        }

        return $next($request);
    }

    /**
     * @throws NoMembershipForThisTeamException
     */
    public function handleInvalidRequest()
    {
        throw NoMembershipForThisTeamException::make();
    }
}
