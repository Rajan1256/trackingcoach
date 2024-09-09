<?php

namespace App\Http\Controllers;

use App\Enum\Roles;
use App\Models\Membership;
use App\Models\Team;
use Auth;
use Illuminate\Http\Request;

use function redirect;

class AssignToTeamController extends Controller
{
    public function __invoke(Request $request, Team $team)
    {
        Membership::create([
            'user_id' => Auth::user()->id,
            'team_id' => $team->id,
            'data'    => [],
            'roles'   => [Roles::ADMIN],
        ]);

        session()->flash('message', __('Successfully assigned to team'));

        return redirect()->back();
    }
}
