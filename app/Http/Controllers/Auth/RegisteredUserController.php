<?php

namespace App\Http\Controllers\Auth;

use App\Enum\Roles;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Mail\NewTeamRegistered;
use App\Models\Membership;
use App\Models\Team;
use App\Models\User;
use App\Models\VerifyUser;
use App\Notifications\RegisteredNotification;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Mail;
use Str;

use function config;
use function current_team;
use function redirect;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create()
    {
        if (current_team()) {
            return redirect(config('app.url').'/register');
        }

        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  Request  $request
     * @return RedirectResponse
     *
     * @throws ValidationException
     */
    public function store(RegisterRequest $request)
    {
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'timezone'   => $request->get('timezone'),
        ]);

        $team = Team::create([
            'user_id'           => $user->id,
            'unlimited_members' => 0,
            'company'           => $request->get('company'),
            'name'              => $request->get('company').' Tracker',
            'fqdn'              => $request->get('fqdn'),
            'timezone'          => $request->get('timezone'),
            'settings'          => [
                'reply_to_email' => $request->get('email'),
            ],
        ]);

        if ($request->hasFile('logo')) {
            $team->addMediaFromRequest('logo')
                ->toMediaCollection('logos', 'media_public');
        }

        Membership::forceCreate([
            'user_id'      => $user->id,
            'team_id'      => $team->id,
            'data'         => [],
            'roles'        => [Roles::ADMIN],
            'company_name' => $request->get('company'),
        ]);

        $verify = VerifyUser::create([
            'user_id' => $user->id,
            'token'   => Str::random(16),
        ]);

        $user->notify(new RegisteredNotification($team, $verify));
        Mail::to('sharon@topmind.com')
            ->send(new NewTeamRegistered($team, $user));
        Mail::to('anne-johan@topmind.com')
            ->send(new NewTeamRegistered($team, $user));

        $team->makeCurrent();
        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
