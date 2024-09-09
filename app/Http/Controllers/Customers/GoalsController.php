<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Goals\StoreGoalRequest;
use App\Http\Requests\Goals\UpdateGoalRequest;
use App\Http\Requests\Goals\UpdateSupporterRequest;
use App\Models\Goal;
use App\Models\User;
use Illuminate\Http\Request;

class GoalsController extends Controller
{
    public function create(Request $request, User $customer)
    {
        return view('customers.goals.create', [
            'customer' => $customer,
        ]);
    }

    public function destroy(Request $request, User $customer, Goal $goal)
    {
        $goal->delete();

        session()->flash('message', __('Successfully deleted the goal'));

        return redirect()->back();
    }

    public function edit(Request $request, User $customer, Goal $goal)
    {
        return view('customers.goals.edit', [
            'customer' => $customer,
            'goal'     => $goal,
        ]);
    }

    public function store(StoreGoalRequest $request, User $customer)
    {
        $customer->goals()->create([
            'team_id' => current_team()->id,
            'name'    => $request->get('name'),
            'scope'   => 'review',
        ]);

        session()->flash('message', __('Successfully added the goal'));

        return redirect()->to(route('customers.reviews.index', [$customer]));
    }

    public function update(UpdateGoalRequest $request, User $customer, Goal $goal)
    {
        $goal->update([
            'name' => $request->get('name'),
        ]);

        session()->flash('message', __('Successfully updated the goal'));

        return redirect()->to(route('customers.reviews.index', [$customer]));
    }
}
