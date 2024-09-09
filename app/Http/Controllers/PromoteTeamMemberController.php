<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class PromoteTeamMemberController extends Controller
{
    public function __invoke(Request $request, User $member)
    {
        $this->authorize('promote', $member);

        $team = current_team();

        $team->user_id = $member->id;
        $team->save();

        session()->flash('message', 'Successfully promoted user to owner.');

        return redirect()->back();
    }
}
