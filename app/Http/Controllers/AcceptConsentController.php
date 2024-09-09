<?php

namespace App\Http\Controllers;

use App\Enum\Roles;
use App\Models\Consent;
use App\Modules\Team\Scopes\TeamAwareScope;
use App\Notifications\UserConsentUpdated;
use App\Notifications\UserGlobalConsentUpdated;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

use function current_team;
use function redirect;

class AcceptConsentController extends Controller
{
    public function index(Request $request)
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

        return view('consents.accept.index', [
            'consents' => $consents->get(),
        ]);
    }

    public function store(Request $request)
    {
        $consents = Consent::active()
            ->notAcceptedBy(Auth::user())
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

        $neededConsents = $consents->get();
        $acceptedConsents = [];
        $acceptedGlobalConsents = [];

        foreach ($request->get('consent', []) as $id) {
            $consent = Consent::withoutGlobalScope(TeamAwareScope::class);

            if (current_team()->owner->id === Auth::user()->id ||
                Auth::user()->hasCurrentTeamRole(Roles::ADMIN)) {
                $consent->where(function (Builder $query) {
                    $query->where('team_id', current_team()?->id)
                        ->orWhereNull('team_id');
                });
            } else {
                $consent->where('team_id', current_team()?->id);
            }

            $consentModel = $consent->find($id);
            $consentModel->users()
                ->attach(Auth::user());

            if ($consentModel->team_id === null) {
                $acceptedGlobalConsents[] = $consentModel;
            } else {
                $acceptedConsents[] = $consentModel;
            }
            $neededConsents = $neededConsents->filter(fn($c) => $c->id != $id);
        }

        $errorMessages = [];
        $neededConsents
            ->each(function ($c) use (&$errorMessages) {
                $errorMessages['consent['.$c->id.']'] = __('You did not agree to this consent.');
            });

        if (count($acceptedConsents) > 0) {
            Auth::user()->notify(new UserConsentUpdated($acceptedConsents, current_team()));
        }

        if (count($acceptedGlobalConsents) > 0) {
            Auth::user()->notify(new UserGlobalConsentUpdated($acceptedGlobalConsents));
        }

        if (count($errorMessages) > 0) {
            return redirect()->back()->withErrors($errorMessages);
        }

        return redirect()->to('dashboard');
    }
}
