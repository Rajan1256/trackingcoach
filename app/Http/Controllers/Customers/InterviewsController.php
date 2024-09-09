<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Interviews\StoreInterviewRequest;
use App\Models\Interview;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;

use function current_team;
use function redirect;

class InterviewsController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Interview::class, 'interview');
    }

    public function index(Request $request, User $customer)
    {
        return view('customers.interviews.index', [
            'customer'   => $customer,
            'interviews' => $customer->interviews,
        ]);
    }

    public function show(Request $request, User $customer, Interview $interview)
    {
        return view('customers.interviews.show', [
            'customer'  => $customer,
            'interview' => $interview,
        ]);
    }

    public function create(Request $request, User $customer)
    {
        return view('customers.interviews.create', [
            'customer' => $customer,
        ]);
    }

    public function edit(Request $request, User $customer, Interview $interview)
    {
        return view('customers.interviews.edit', [
            'customer'  => $customer,
            'interview' => $interview,
        ]);
    }

    public function update(Request $request, User $customer, Interview $interview)
    {
        $interview->update([
            'date'     => $request->get('date'),
            'continue' => array_filter($request->get('continue', [])),
            'start'    => array_filter($request->get('start', [])),
            'stop'     => array_filter($request->get('stop', [])),
            'best'     => array_filter($request->get('best', [])),
            'worst'    => array_filter($request->get('worst', [])),
        ]);

        session()->flash('message', __('Successfully updated interview'));

        return redirect(route('customers.interviews.show', [$customer, $interview]));
    }

    public function store(StoreInterviewRequest $request, User $customer)
    {
        $interview = Interview::create([
            'date'      => $request->get('date'),
            'continue'  => array_filter($request->get('continue', [])),
            'start'     => array_filter($request->get('start', [])),
            'stop'      => array_filter($request->get('stop', [])),
            'best'      => array_filter($request->get('best', [])),
            'worst'     => array_filter($request->get('worst', [])),
            'team_id'   => current_team()->id,
            'user_id'   => $customer->id,
            'author_id' => Auth::user()->id,
        ]);

        session()->flash('message', __('Successfully created interview'));

        return redirect(route('customers.interviews.show', [$customer, $interview]));
    }

    public function destroy(Request $request, User $customer, Interview $interview)
    {
        $interview->delete();

        session()->flash('message', __('Interview successfully deleted'));

        return redirect(route('customers.interviews.index', [$customer]));
    }
}
