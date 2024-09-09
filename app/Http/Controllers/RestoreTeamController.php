<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;

class RestoreTeamController extends Controller
{
    public function __invoke(Request $request, Team $team)
    {
        $this->authorize('restore', [$team]);

        $team->restore();

        session()->flash('message', __('Team successfully restored'));

        return redirect()->back();
    }
}
