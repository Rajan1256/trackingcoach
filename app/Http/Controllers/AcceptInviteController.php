<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcceptInviteRequest;
use App\Models\Membership;
use App\Models\User;
use App\Models\UserInvite;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use function collect;

class AcceptInviteController extends Controller
{
    public function show(Request $request, string $token)
    {
        $invite = UserInvite::where('token', $token)->firstOrFail();

        if ($user = User::where('email', $invite->email)->first()) {
            $membership = Membership::where('user_id', $user->id)
                ->where('team_id', $invite->team_id)
                ->first();

            if (!$membership) {
                $membership = Membership::create([
                    'user_id' => $user->id,
                    'team_id' => $invite->team_id,
                    'data'    => [],
                    'roles'   => $invite->data['roles'],
                ]);

                $settings = $user->getSettings($invite->team);

                foreach ($request->all() as $key => $value) {
                    $settings->$key = $value;
                }

                foreach ($invite->data as $key => $value) {
                    $settings->$key = $value;
                }

                $settings->save();
            }

            if (!$membership->roles) {
                $membership->roles = collect();
            }

            $membership->roles = $membership->roles->add($invite->data['roles']);
            $membership->save();

            $invite->expires_at = Carbon::now();
            $invite->save();

            Auth::login($user);

            return redirect()->to(route('dashboard'));
        }

        return view('accept.invite', [
            'invite'     => $invite,
            'first_name' => $invite->data['first_name'] ?? '',
            'last_name'  => $invite->data['last_name'] ?? '',
            'email'      => $invite->email,
        ]);
    }

    public function store(AcceptInviteRequest $request, string $token)
    {
        $invite = UserInvite::where('token', $token)->firstOrFail();
        $user = User::create([
            'first_name' => $invite->data['first_name'],
            'last_name'  => $invite->data['last_name'],
            'email'      => $invite->email,
            'password'   => Hash::make($request->get('password')),
        ]);

        $membership = Membership::create([
            'user_id' => $user->id,
            'team_id' => $invite->team_id,
            'data'    => [],
            'roles'   => $invite->data['roles'],
        ]);

        $settings = $user->getSettings($invite->team);

        foreach ($request->all() as $key => $value) {
            $settings->$key = $value;
        }

        foreach ($invite->data as $key => $value) {
            $settings->$key = $value;
        }

        $settings->save();

        $invite->expires_at = Carbon::now();
        $invite->save();

        Auth::login($user);

        return redirect()->to(route('dashboard'));
    }
}
