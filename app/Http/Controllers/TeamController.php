<?php

namespace App\Http\Controllers;

use App\Enum\Roles;
use App\Http\Requests\Teams\StoreTeamRequest;
use App\Mail\InviteToTeam;
use App\Models\Membership;
use App\Models\Team;
use App\Models\User;
use App\Models\UserInvite;
use App\Notifications\NotifyAboutNewTeam;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Mail;

use function current_team;
use function redirect;

class TeamController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Team::class, 'team');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        return view('teams.create');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Team  $team
     * @return Response
     */
    public function destroy(Request $request, Team $team)
    {
        $team->delete();

        session()->flash('message', __('Team successfully deleted'));

        return redirect()->to(route('teams.index'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Team  $team
     * @return Response
     */
    public function edit(Request $request, Team $team)
    {
        return view('teams.edit', [
            'team' => $team,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $teams = Team::withTrashed()->paginate(15);

        return view('teams.index', [
            'teams' => $teams,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  Team  $team
     * @return Response
     */
    public function show(Request $request, Team $team)
    {
        return view('teams.show', [
            'team' => $team,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(StoreTeamRequest $request)
    {
        $mainTeam = current_team();

        $team = Team::create([
            'user_id'           => Auth::user()->id,
            'unlimited_members' => $request->get('unlimited_members') ?? 0,
            'company'           => $request->get('company'),
            'name'              => $request->get('name'),
            'fqdn'              => $request->get('fqdn'),
            'timezone'          => $request->get('timezone'),
            'settings'          => [
                'reply_to_email' => $request->get('settings')['reply_to_email'],
                'signature_line' => $request->get('settings')['signature_line'],
                'color'          => $request->get('settings')['color'],
            ],
        ]);

        if ($request->hasFile('logo')) {
            $team->addMediaFromRequest('logo')
                ->toMediaCollection('logos', 'media_public');
        }

        Membership::forceCreate([
            'user_id' => Auth::user()->id,
            'team_id' => $team->id,
            'data'    => [],
            'roles'   => [Roles::ADMIN],
        ]);

        $team->makeCurrent();

        $user = User::where('email', $request->get('email'))->first();

        if (!$user) {
            do {
                $token = Str::random(20);
            } while (UserInvite::where('token', $token)->first());

            UserInvite::create([
                'team_id'    => $team->id,
                'email'      => $request->get('email'),
                'data'       => array_merge($request->except(['email']), ['roles' => [Roles::ADMIN]]),
                'token'      => $token,
                'expires_at' => Carbon::now()->addDays(30),
            ]);

            Mail::to($request->get('email'),)->send(new InviteToTeam($team,
                $request->get('first_name'), $request->get('last_name'), $token));
        } elseif ($user->id != auth()->user()->id) {
            Membership::create([
                'user_id' => $user->id,
                'team_id' => $team->id,
                'data'    => [],
                'roles'   => [Roles::ADMIN],
            ]);

            $settings = $user->getSettings();

            foreach ($request->all() as $key => $value) {
                $settings->$key = $value;
            }

            $settings->save();

            $user->notify(new NotifyAboutNewTeam($team));
        }

        session()->flash('message', __('Successfully created team.'));

        $mainTeam->makeCurrent();

        return redirect()->to(route('teams.show', [
            $team,
        ]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Team  $team
     * @return Response
     */
    public function update(Request $request, Team $team)
    {
        $team->update([
            'company'           => $request->get('company'),
            'unlimited_members' => $request->get('unlimited_members') ?? 0,
            'name'              => $request->get('name'),
            'timezone'          => $request->get('timezone'),
            'settings'          => [
                'reply_to_email' => $request->get('settings')['reply_to_email'],
                'signature_line' => $request->get('settings')['signature_line'],
                'color'          => $request->get('settings')['color'],
            ],
        ]);

        if ($request->hasFile('logo')) {
            $team->addMediaFromRequest('logo')
                ->toMediaCollection('logos', 'media_public');
        }

        session()->flash('message', __('Successfully updated team.'));

        return redirect()->to(route('teams.show', [
            $team,
        ]));
    }
}
