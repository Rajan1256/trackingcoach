<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Auth;
use Illuminate\Http\Request;

class UnassignFromTeamController extends Controller
{
    public function __invoke(Request $request, Team $team)
    {
        $team->users()->detach(Auth::user());

        session()->flash('message', __('Successfully unassigned to team'));

        return redirect()->back();
    }
}
