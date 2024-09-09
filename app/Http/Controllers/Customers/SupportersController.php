<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supporters\StoreSupporterRequest;
use App\Http\Requests\Supporters\UpdateSupporterRequest;
use App\Models\Supporter;
use App\Models\User;
use Illuminate\Http\Request;

class SupportersController extends Controller
{
    public function create(Request $request, User $customer)
    {
        return view('customers.supporters.create', [
            'customer' => $customer,
        ]);
    }

    public function store(StoreSupporterRequest $request, User $customer)
    {
        $customer->supporters()->create([
            'team_id'             => current_team()->id,
            'first_name'          => $request->get('first_name'),
            'last_name'           => $request->get('last_name'),
            'email'               => $request->get('email'),
            'phone'               => $request->get('phone'),
            'relationship'        => $request->get('relationship'),
            'notification_method' => $request->get('notification_method'),
            'personal_note'       => $request->get('personal_note'),
            'locale'              => $request->get('locale'),
        ]);

        session()->flash('message', __('Successfully added the supporter'));

        return redirect()->to(route('customers.reviews.index', [$customer]));
    }

    public function edit(Request $request, User $customer, Supporter $supporter)
    {
        return view('customers.supporters.edit', [
            'customer'  => $customer,
            'supporter' => $supporter,
        ]);
    }

    public function update(UpdateSupporterRequest $request, User $customer, Supporter $supporter)
    {
        $supporter->update([
            'first_name'          => $request->get('first_name'),
            'last_name'           => $request->get('last_name'),
            'email'               => $request->get('email'),
            'phone'               => $request->get('phone'),
            'relationship'        => $request->get('relationship'),
            'notification_method' => $request->get('notification_method'),
            'personal_note'       => $request->get('personal_note'),
            'locale'              => $request->get('locale'),
        ]);

        session()->flash('message', __('Successfully updated the supporter'));

        return redirect()->to(route('customers.reviews.index', [$customer]));
    }

    public function destroy(Request $request, User $customer, Supporter $supporter)
    {
        $supporter->delete();

        session()->flash('message', __('Successfully deleted the supporter'));

        return redirect()->back();
    }
}
