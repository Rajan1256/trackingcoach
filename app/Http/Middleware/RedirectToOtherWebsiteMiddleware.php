<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use function current_team;
use function redirect;

class RedirectToOtherWebsiteMiddleware
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
        if ($request->is('/') && !current_team()) {
            return redirect('https://www.trackingcoachsystem.com');
        }

        return $next($request);
    }
}
