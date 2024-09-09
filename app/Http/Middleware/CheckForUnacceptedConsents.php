<?php

namespace App\Http\Middleware;

use App\Enum\Roles;
use App\Models\Consent;
use App\Modules\Team\Scopes\TeamAwareScope;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckForUnacceptedConsents
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
        $consents = Consent::active()
            ->withoutGlobalScope(TeamAwareScope::class);

        if (current_team()->owner->id === Auth::user()->id ||
            Auth::user()->hasCurrentTeamRole(Roles::ADMIN)) {
            $consents->where(function (Builder $query) {
                $query->where('team_id', current_team()?->id)
                    ->orWhereNull('team_id');
            });
        } else {
            $consents->where('team_id', current_team()?->id);
        }

        /** @var Collection $activeConsents */
        $activeConsents = $consents->get();

        $notAcceptedConsents = $activeConsents->filter(function ($consent) {
            return !$consent->users->contains(Auth::user());
        });

        if ($activeConsents->count() > 0 && $notAcceptedConsents->count() > 0) {
            return redirect()->to(route('consents.accept.index'));
        }

        return $next($request);
    }
}
