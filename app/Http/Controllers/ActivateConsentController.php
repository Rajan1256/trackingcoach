<?php

namespace App\Http\Controllers;

use App\Models\Consent;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ActivateConsentController extends Controller
{
    public function __invoke(Request $request, Consent $consent)
    {
        $consent->activated_at = Carbon::now();
        $consent->save();

        $consent->users()->save(Auth::user());

        session()->flash('message', __('Consent activated'));

        return redirect()->back();
    }
}
