<?php

namespace App\Http\Controllers;

use App\Models\VerifyUser;
use Illuminate\Http\Request;

use function redirect;
use function route;

class VerifyController extends Controller
{
    public function index(Request $request)
    {
        return view('verify.index');
    }

    public function show(Request $request, VerifyUser $verifyUser)
    {
        $verifyUser->delete();

        session()->flash('account-verified', __('Your account has successfully been verified'));

        return redirect(route('dashboard'));
    }
}
