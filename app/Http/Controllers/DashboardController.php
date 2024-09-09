<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        if (!Auth::user()) {
            return redirect('https://tackingcoachsystem.com');
        }

        if (Auth::user()->currentTeam) {
            Auth::user()->currentTeam->makeCurrent();
            return redirect(route('dashboard'));
        }

        if (Auth::user()->teams->count() === 1) {
            Auth::user()->teams->first()->makeCurrent();
            return redirect(route('dashboard'));
        }

        return view('team-select');
    }
}
