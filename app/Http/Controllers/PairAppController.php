<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class PairAppController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();
        $settings = $user->getSettings();

        if (!$settings->paired_app_token) {
            $settings->paired_app_token = md5(time().uniqid());
            $settings->save();
        }

        return view('app.pair', [
            'token' => $settings->paired_app_token,
        ]);
    }
}
