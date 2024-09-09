<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Models\Invite;
use App\Models\Review;
use App\Models\Supporter;
use App\Models\User;
use App\Notifications\SendReviewSupporterInvite;
use Illuminate\Http\Request;

class SendInviteController extends Controller
{
    public function __invoke(Request $request, User $customer, Review $review, Supporter $supporter)
    {
        $reviewInvitation = Invite::newReviewInvite($customer, $review, $supporter);
        $supporter->notify((new SendReviewSupporterInvite($reviewInvitation)));

        session()->flash('message', __('Supporter successfully notified'));

        return redirect()->back();
    }
}
