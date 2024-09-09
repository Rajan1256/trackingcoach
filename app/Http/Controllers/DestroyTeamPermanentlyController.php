<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;

class DestroyTeamPermanentlyController extends Controller
{
    public function __invoke(Request $request, Team $team)
    {
        $team->forceDelete();

        session()->flash('message', __('Team successfully permanently deleted'));

        return redirect()->to(route('teams.index'));
    }
}
