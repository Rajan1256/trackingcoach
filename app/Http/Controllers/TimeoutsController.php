<?php

namespace App\Http\Controllers;

use App\Http\Requests\Timeouts\StoreTimeoutRequest;
use App\Http\Requests\Timeouts\UpdateTimeoutRequest;
use App\Models\Timeout;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Gate;
use Illuminate\Http\Request;

class TimeoutsController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!Gate::allows('viewAny-globalTimeout'), 403);

        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month', Carbon::now()->month);

        $start = CarbonImmutable::createFromDate($year, $month, 1)->startOfDay();
        $end = $start->endOfMonth();

        $prev = $start->subMonth();
        $next = $start->addMonth();

        $timeouts = Timeout::between($start, $end)->orderBy('start')->get();

        return view('timeouts.index', [
            'start'    => $start,
            'end'      => $end,
            'prev'     => $prev,
            'next'     => $next,
            'timeouts' => $timeouts,
        ]);
    }

    public function create(Request $request)
    {
        abort_if(!Gate::allows('create-globalTimeout'), 403);

        $customers = current_team()->users()->isCustomer()->get();

        return view('timeouts.create', [
            'customers' => $customers,
        ]);
    }

    public function store(StoreTimeoutRequest $request)
    {
        abort_if(!Gate::allows('create-globalTimeout'), 403);

        Timeout::create([
            'user_id' => $request->get('customer'),
            'team_id' => current_team()->id,
            'start'   => $request->get('start'),
            'end'     => $request->get('end'),
        ]);

        session()->flash('message', __('Successfully created timeout'));

        return redirect()->to(route('timeouts.index'));
    }

    public function edit(Request $request, Timeout $timeout)
    {
        abort_if(!Gate::allows('update-globalTimeout', [$timeout]), 403);

        $customers = current_team()->users()->isCustomer()->get();

        return view('timeouts.edit', [
            'timeout'   => $timeout,
            'customers' => $customers,
        ]);
    }

    public function update(UpdateTimeoutRequest $request, Timeout $timeout)
    {
        abort_if(!Gate::allows('update-globalTimeout', [$timeout]), 403);

        $timeout->update([
            'start' => $request->get('start'),
            'end'   => $request->get('end'),
        ]);

        session()->flash('message', __('Successfully updated timeout'));

        return redirect()->to(route('timeouts.index'));
    }

    public function destroy(Request $request, Timeout $timeout)
    {
        abort_if(!Gate::allows('delete-globalTimeout', [$timeout]), 403);

        $timeout->delete();

        session()->flash('message', __('Successfully deleted timeout'));

        return redirect()->to(route('timeouts.index'));
    }
}
