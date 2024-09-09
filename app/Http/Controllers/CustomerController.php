<?php

namespace App\Http\Controllers;

use App\Enum\Roles;
use App\Events\DeleteTeamUser;
use App\Http\Requests\Customers\StoreCustomerRequest;
use App\Http\Requests\Customers\UpdateCustomerRequest;
use App\Mail\InviteToTeam;
use App\Models\CoachUser;
use App\Models\Membership;
use App\Models\PhysiologistUser;
use App\Models\User;
use App\Models\UserInvite;
use App\Notifications\NotifyAboutNewTeam;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mail;

use function collect;
use function current_team;
use function redirect;
use function view;

class CustomerController extends Controller
{
    public function create(Request $request)
    {
        $this->authorize('create', User::class);

        $coaches = current_team()->users()->isCoach()->get()->sortBy(function ($model) {
            return $model->name;
        });
        $physiologists = current_team()->users()->isPhysiologists()->get()->sortBy(function ($model) {
            return $model->name;
        });
        return view('customers.create', [
            'coaches'       => $coaches,
            'physiologists' => $physiologists,
        ]);
    }

    public function destroy(Request $request, User $customer)
    {
        $team = current_team();
        $membership = Membership::where('user_id', $customer->id)
            ->where('team_id', $team->id)
            ->first();

        if ($request->get('force') == 1) {
            DeleteTeamUser::dispatch($customer, $team);
        } else {
            if ($membership->roles->contains(Roles::CUSTOMER)) {
                $membership->roles = $membership->roles->reject(fn($r) => $r == Roles::CUSTOMER);
                $membership->roles = $membership->roles->add('client_archived');
                session()->flash('message', __('Successfully archived customer.'));
            } else {
                $membership->roles = $membership->roles->reject(fn($r) => $r == 'client_archived');
                $membership->roles = $membership->roles->add(Roles::CUSTOMER);
                session()->flash('message', __('Successfully restored customer.'));
            }

            $membership->save();
        }

        return redirect()->back();
    }

    public function edit(Request $request, User $customer)
    {
        $coaches = current_team()->users()->isCoach()->get()->sortBy(function ($model) {
            return $model->name;
        });
        $physiologists = current_team()->users()->isPhysiologists()->get()->sortBy(function ($model) {
            return $model->name;
        });
        $settings = $customer->getSettings();

        return view('customers.edit', [
            'customer'      => $customer,
            'coaches'       => $coaches,
            'physiologists' => $physiologists,
            'settings'      => $settings,
        ]);
    }

    public function show(Request $request, User $customer)
    {
        abort_unless($customer->belongsToTeam(current_team()), 404);

        $overallScore = $customer->getOverallScore();
        $weekReports = $customer->listWeekReports();
        $monthReports = $customer->listMonthReports();
        $lastWeeks = $customer->getLastWeeksGraphData(23, null, 4);
        $growthData = $customer->getGrowthDetails();
        $settings = $customer->getSettings();
        $dayReports = $customer->listDayReports($customer);
        $verbatimReports = $customer->verbatimReports($customer);

        return view('customers.show', [
            'customer'     => $customer,
            'dayReports'        => $dayReports,
            'verbatimReports'   => $verbatimReports,
            'overallScore' => $overallScore,
            'weekReports'  => $weekReports,
            'monthReports' => $monthReports,
            'lastWeeks'    => $lastWeeks,
            'growthData'   => $growthData,
            'settings'     => $settings,
        ]);
    }

    public function store(StoreCustomerRequest $request)
    {
        $user = User::where('email', $request->get('email'))->first();

        if ($user) {
            $membership = Membership::where('user_id', $user->id)
                ->where('team_id', current_team()->id)
                ->first();

            if (!$membership) {
                $membership = Membership::create([
                    'user_id' => $user->id,
                    'team_id' => current_team()->id,
                    'data'    => [],
                ]);

                $settings = $user->getSettings();

                foreach ($request->all() as $key => $value) {
                    $settings->$key = $value;
                }
            }

            if (empty($membership->roles)) {
                $membership->roles = collect();
            }

            $membership->roles = $membership->roles->add(Roles::CUSTOMER);
            $membership->save();

            $user->notify(new NotifyAboutNewTeam(current_team()));
        } else {
            do {
                $token = Str::random(20);
            } while (UserInvite::where('token', $token)->first());

            UserInvite::create([
                'team_id'    => current_team()->id,
                'email'      => $request->get('email'),
                'data'       => array_merge($request->except(['email']), ['roles' => [Roles::CUSTOMER]]),
                'token'      => $token,
                'expires_at' => Carbon::now()->addDays(30),
            ]);

            Mail::to($request->get('email'),)->send(new InviteToTeam(current_team(),
                $request->get('first_name'), $request->get('last_name'), $token));
        }

        session()->flash('message', __('Customer invited'));

        return redirect()->to(route('customers'));
    }

    public function update(UpdateCustomerRequest $request, User $customer)
    {
        $settings = $customer->getSettings();
        $settings->days_per_week = $request->get('days_per_week');
        $settings->filled_auto_invite_time = $request->get('filled_auto_invite_time');
        $settings->save();

        $coachUser = CoachUser::where('coach_id', $customer->coach?->id)
            ->where('user_id', $customer->id)
            ->first();
        if (!$coachUser && !empty($request->get('coach'))) {
            CoachUser::create([
                'team_id'  => current_team()->id,
                'user_id'  => $customer->id,
                'coach_id' => $request->get('coach'),
            ]);
        } elseif ($coachUser && !empty($request->get('coach'))) {
            $coachUser->coach_id = $request->get('coach');
            $coachUser->save();
        } elseif ($coachUser) {
            $coachUser->delete();
        }

        $physiologist = PhysiologistUser::where('physiologist_id', $customer->physiologist?->id)
            ->where('user_id', $customer->id)
            ->first();
        if (!$physiologist && !empty($request->get('physiologist'))) {
            PhysiologistUser::create([
                'team_id'         => current_team()->id,
                'user_id'         => $customer->id,
                'physiologist_id' => $request->get('physiologist'),
            ]);
        } elseif ($physiologist && !empty($request->get('physiologist'))) {
            $physiologist->physiologist_id = $request->get('physiologist');
            $physiologist->save();
        } elseif ($physiologist) {
            $physiologist->delete();
        }

        session()->flash('message', __('Successfully updated customer.'));

        return redirect()->back();
    }
}
