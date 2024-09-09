<?php

namespace App\Modules\Team\Http\Middleware;

use App\Models\Team;
use App\Modules\Team\Exceptions\NoMembershipForThisTeamException;
use Auth;
use Closure;

use function redirect;

class HasNoMembershipForTeam
{
    /**
     * @throws NoMembershipForThisTeamException
     */
    public function handle($request, Closure $next)
    {
        if (Auth::user() && Auth::user()->belongsToTeam(Team::current())) {
            return redirect()->to('dashboard');
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
