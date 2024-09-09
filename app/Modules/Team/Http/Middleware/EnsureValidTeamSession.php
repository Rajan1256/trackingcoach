<?php

namespace App\Modules\Team\Http\Middleware;

use App\Models\Team;
use Closure;
use Symfony\Component\HttpFoundation\Response;

class EnsureValidTeamSession
{
    public function handle($request, Closure $next)
    {
        $sessionKey = 'ensure_valid_team_session_team_id';

        if ($request->session()->get($sessionKey) !== app(Team::getServiceContainerKey())?->id) {
            $request->session()->forget($sessionKey);
        }

        if (!$request->session()->has($sessionKey)) {
            $request->session()->put($sessionKey, app(Team::getServiceContainerKey())->id);

            return $next($request);
        }

        if ($request->session()->get($sessionKey) !== app(Team::getServiceContainerKey())->id) {
            $this->handleInvalidTeamSession($request);
        }

        return $next($request);
    }

    protected function handleInvalidTeamSession($request)
    {
        abort(Response::HTTP_UNAUTHORIZED);
    }
}
