<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

class BillingPortalController extends Controller
{
    public function index(Request $request)
    {
        return view('billing.index');
    }

    public function view($plan)
    {
        $selectedPlan = null;
        foreach (config('cashier.plans') as $cPlan) {
            if ($cPlan['monthly_id'] === $plan) {
                $selectedPlan = $cPlan;
            }
        }

        if (current_team()->users()->isCustomer()->count() > $selectedPlan['options']['max_customers']) {
            session()->flash('error',
                __('You can not switch to this plan because you have too many users'));
            return redirect()->back();
        }

        if (current_team()->subscription() !== null) {
            if (current_team()->subscription()->onGracePeriod()) {
                current_team()->subscription()->resume();
            } elseif (current_team()->subscription()->canceled()) {
                current_team()->subscription()->delete();
            }
        }

        if (current_team()->subscriptions()->count() > 0) {
            try {
                current_team()->subscription()
                    ->updateQuantity(current_team()->users()->isBillableUser()->count());
                current_team()->subscription()
                    ->swapAndInvoice($plan);
                session()->flash('message', __('Successfully changed your subscription'));
            } catch (Exception $exception) {
                session()->flash('error',
                    __('There was a problem updating your subscription. Update your payment methods and try again. If this issue keeps happening please contact customer support'));
            }

            return redirect()->back();
        }

        try {
            $subscription = current_team()
                ->newSubscription('default', $plan)
                ->quantity(current_team()->users()->isBillableUser()->count());

            if (is_null(current_team()->stripe_id)) {
                $subscription->trialDays(15);
            } else {
                $subscription->skipTrial();
            }

            return $subscription->checkout(['payment_method_types' => ['card', 'sepa_debit'], 'automatic_tax' => ['enabled' => true]]);
        } catch (Exception $exception) {
            session()->flash('error',
                __('There was a problem creating your subscription. Update your payment methods and try again. If this issue keeps happening please contact customer support'));
        }

        return redirect()->back();
    }
}
