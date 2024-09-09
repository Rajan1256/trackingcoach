<?php

namespace App\Http\Middleware;

use App\Enum\Roles;
use Auth;
use Closure;
use Illuminate\Http\Request;

use function current_team;
use function redirect;

class NeedActiveSubscription
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
        if (!current_team()->subscribed() && Auth::user()->hasCurrentTeamRole([Roles::ADMIN])) {
            return redirect(route('billing.portal'));
        }

        return $next($request);
    }
}
