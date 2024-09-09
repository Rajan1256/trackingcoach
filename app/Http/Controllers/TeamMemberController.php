<?php

namespace App\Http\Controllers;

use App\Enum\Roles;
use App\Events\DeleteTeamUser;
use App\Http\Requests\TeamMembers\StoreTeamMemberRequest;
use App\Http\Requests\TeamMembers\UpdateTeamMemberRequest;
use App\Mail\InviteToTeam;
use App\Models\Membership;
use App\Models\User;
use App\Models\UserInvite;
use App\Notifications\NotifyAboutNewTeam;
use Carbon\Carbon;
use Gate;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Mail;
use Throwable;

use function abort_if;
use function array_filter;
use function collect;
use function current_team;
use function in_array;
use function redirect;

class TeamMemberController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        abort_if(!Gate::allows('create-teamMember'), 403);

        return view('teammembers.create');
    }

    public function destroy(Request $request, User $member)
    {
        abort_if(!Gate::allows('delete-teamMember', [$member]), 403);

        $settings = $member->getSettings();
        $settings->roles = array_filter($settings->roles, function ($value) {
            return $value === Roles::CUSTOMER || $value === 'client_archived';
        });
        $settings->save();

        if (count($settings->roles) > 0) {
            DeleteTeamUser::dispatch($member, current_team());
        }

        session()->flash('message', 'Successfully removed user from team.');

        return redirect()->back();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        abort_if(!Gate::allows('viewAny-teamMember'), 403);

        $members = current_team()
            ->users
            ->filter(fn($user) => $user->membership->roles?->contains(fn(
                $val,
                $key
            ) => in_array($val, [Roles::ADMIN, Roles::COACH, Roles::PHYSIOLOGIST])));

        return view('teammembers.index', [
            'members' => $members,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  User  $member
     * @return Application|Factory|View
     * @throws Throwable
     */
    public function show(User $member)
    {
        abort_if(!Gate::allows('view-teamMember', [$member]), 403);

        return view('teammembers.show', [
            'settings' => $member->getSettings(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreTeamMemberRequest  $request
     * @return Application|RedirectResponse|Redirector
     * @throws Throwable
     */
    public function store(StoreTeamMemberRequest $request)
    {
        abort_if(!Gate::allows('create-teamMember'), 403);

        $user = User::where('email', $request->get('email'))->first();

        if ($user) {
            Membership::updateOrCreate([
                'user_id' => $user->id,
                'team_id' => current_team()->id,
            ], [
                'data' => [],
            ]);

            $settings = $user->getSettings();

            foreach ($request->all() as $key => $value) {
                $settings->$key = $value;
            }

            if (empty($settings->roles)) {
                $settings->roles = collect();
            }

            $settings->roles = $settings->roles->merge($request->get('roles'));
            $settings->save();

            $user->notify(new NotifyAboutNewTeam(current_team()));
        } else {
            do {
                $token = Str::random(20);
            } while (UserInvite::where('token', $token)->first());

            UserInvite::create([
                'team_id'    => current_team()->id,
                'email'      => $request->get('email'),
                'data'       => $request->except(['email']),
                'token'      => $token,
                'expires_at' => Carbon::now()->addDays(30),
            ]);

            Mail::to($request->get('email'),)->send(new InviteToTeam(current_team(),
                $request->get('first_name'), $request->get('last_name'), $token));
        }

        session()->flash('message', __('Team member invited'));

        return redirect()->to(route('teams.members'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateTeamMemberRequest  $request
     * @param  User  $member
     * @return RedirectResponse
     * @throws Throwable
     */
    public function update(UpdateTeamMemberRequest $request, User $member)
    {
        abort_if(!Gate::allows('update-teamMember', [$member]), 403);

        $settings = $member->getSettings();

        foreach ($request->all() as $key => $value) {
            $settings->$key = $value;
        }

        $settings->save();
        return redirect()->back();
    }
}
