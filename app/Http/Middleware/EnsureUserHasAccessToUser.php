<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

use function is_string;

class EnsureUserHasAccessToUser
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
        $customer = $request->route()->parameter('customer');
        if (is_string($customer)) {
            $customer = User::findOrFail($customer);
        }

        abort_if(
            !auth()->user()->can('view', $customer),
            404
        );

        return $next($request);
    }
}
