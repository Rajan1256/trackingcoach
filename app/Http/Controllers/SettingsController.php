<?php

namespace App\Http\Controllers;

use App\Http\Requests\Settings\UpdateSettingsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use function redirect;

class SettingsController extends Controller
{
    public function show(Request $request)
    {
        return view('settings.show', [
            'settings' => Auth::user()->getSettings(),
        ]);
    }

    public function update(UpdateSettingsRequest $request)
    {
        $settings = Auth::user()->getSettings();

        foreach ($request->except(['password']) as $key => $value) {
            $settings->$key = $value;
        }

        $settings->save();

        if (!empty($request->get('password'))) {
            Auth::user()->password = Hash::make($request->get('password'));
            Auth::user()->save();
        }

        session()->flash('message', __('Successfully saved your settings'));

        return redirect()->back();
    }
}
