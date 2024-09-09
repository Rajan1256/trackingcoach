<?php

namespace App\Modules\Team\Http\Middleware;

use App\Models\Team;
use App\Modules\Team\Exceptions\NoCurrentTeamException;
use Closure;

class NeedsTeam
{
    /**
     * @throws NoCurrentTeamException
     */
    public function handle($request, Closure $next)
    {
        if (!Team::checkCurrent()) {
            return redirect(route('select-team'));
            $this->handleInvalidRequest();
        }

        return $next($request);
    }

    /**
     * @throws NoCurrentTeamException
     */
    public function handleInvalidRequest()
    {
        abort(404);
        throw NoCurrentTeamException::make();
    }
}
