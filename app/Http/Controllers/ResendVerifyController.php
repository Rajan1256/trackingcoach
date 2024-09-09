<?php

namespace App\Http\Controllers;

use App\Models\VerifyUser;
use App\Notifications\RegisteredNotification;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use function redirect;

class ResendVerifyController extends Controller
{
    public function __invoke(Request $request)
    {
        $verify = VerifyUser::updateOrCreate([
            'user_id' => Auth::user()->id,
        ], [
            'token' => Str::random(16),
        ]);


        Auth::user()->notify(new RegisteredNotification(Auth::user()->teams()->first(), $verify));

        session()->flash('resend', __('Your verification email has been resent.'));

        return redirect()->back();
    }
}
