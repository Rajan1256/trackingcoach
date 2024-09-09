<?php

namespace App\Http\Controllers;

use App\Models\Consent;
use Illuminate\Http\Request;

class DeactivateConsentController extends Controller
{
    public function __invoke(Request $request, Consent $consent)
    {
        $consent->activated_at = null;
        $consent->save();

        session()->flash('message', __('Consent deactivated'));

        return redirect()->back();
    }
}
