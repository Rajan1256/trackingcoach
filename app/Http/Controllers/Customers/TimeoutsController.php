<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Timeouts\Customers\StoreTimeoutRequest;
use App\Http\Requests\Timeouts\Customers\UpdateTimeoutRequest;
use App\Models\Timeout;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class TimeoutsController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Timeout::class, 'timeout');
    }

    public function index(Request $request, User $customer)
    {
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month', Carbon::now()->month);

        $start = CarbonImmutable::createFromDate($year, $month, 1)->startOfDay();
        $end = $start->endOfMonth();

        $prev = $start->subMonth();
        $next = $start->addMonth();

        $timeouts = $customer->timeouts()->between($start, $end)->orderBy('start')->get();

        return view('customers.timeouts.index', [
            'start'    => $start,
            'end'      => $end,
            'prev'     => $prev,
            'next'     => $next,
            'customer' => $customer,
            'timeouts' => $timeouts,
        ]);
    }

    public function create(Request $request, User $customer)
    {
        return view('customers.timeouts.create', [
            'customer' => $customer,
        ]);
    }

    public function store(StoreTimeoutRequest $request, User $customer)
    {
        Timeout::create([
            'user_id' => $customer->id,
            'team_id' => current_team()->id,
            'start'   => $request->get('start'),
            'end'     => $request->get('end'),
        ]);

        session()->flash('message', __('Successfully created timeout'));

        return redirect()->to(route('customers.timeouts.index', [
            'customer' => $customer,
        ]));
    }

    public function edit(Request $request, User $customer, Timeout $timeout)
    {
        return view('customers.timeouts.edit', [
            'customer' => $customer,
            'timeout'  => $timeout,
        ]);
    }

    public function update(UpdateTimeoutRequest $request, User $customer, Timeout $timeout)
    {
        $timeout->update([
            'start' => $request->get('start'),
            'end'   => $request->get('end'),
        ]);

        session()->flash('message', __('Successfully updated timeout'));

        return redirect()->to(route('customers.timeouts.index', [
            'customer' => $customer,
        ]));
    }

    public function destroy(Request $request, User $customer, Timeout $timeout)
    {
        $timeout->delete();

        session()->flash('message', __('Successfully deleted timeout'));

        return redirect()->to(route('customers.timeouts.index', [
            'customer' => $customer,
        ]));
    }
}
