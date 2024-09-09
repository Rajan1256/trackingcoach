<?php

namespace App\Modules\Team\Exceptions;

use App\Models\Team;
use Exception;
use Illuminate\Support\Facades\Auth;

use function current_team;

class NoMembershipForThisTeamException extends Exception
{
    public static function make()
    {
        return new static('You have no active membership on this team.');
    }

    public function render()
    {
        if ($lastKnownTeam = current_team() && Auth::user()->belongsToTeam(Team::current())) {
            $url = parse_url(request()->url());
            $url['host'] = $lastKnownTeam->fqdn;
            return redirect()->to("{$url['scheme']}://{$url['host']}{$url['path']}");
        }

        if ($firstTeam = Auth::user()->teams()->first()) {
            $url = parse_url(request()->url());
            $url['host'] = $firstTeam->fqdn;
            return redirect()->to("{$url['scheme']}://{$url['host']}{$url['path']}");
        }

        return redirect()->route('no-teams');
    }
}
