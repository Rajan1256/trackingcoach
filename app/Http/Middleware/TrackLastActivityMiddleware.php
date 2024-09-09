<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

use function current_team;

class TrackLastActivityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::user() && current_team()) {
            $user = Auth::user();
            $user->current_team_id = current_team()->id;
            $user->last_activity = Carbon::now();
            $user->save();
        }

        return $next($request);
    }
}
