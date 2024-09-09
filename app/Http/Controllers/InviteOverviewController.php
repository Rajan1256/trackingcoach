<?php

namespace App\Http\Controllers;

use App\Enum\Roles;
use App\Models\Membership;
use Illuminate\Support\Facades\Gate;
use Request;

use function abort_unless;

class InviteOverviewController extends Controller
{
    public function __invoke(Request $request, $type, $date)
    {
        abort_unless(Gate::check('viewHorizon'), 401);

        $customers = Membership::whereJsonContains('roles', Roles::CUSTOMER)
            ->withoutGlobalScopes()
            ->with([
                'user',
                'team',
                'user.invites' => function ($builder) use ($type, $date) {
                    $builder->withoutGlobalScopes();
                    $builder->type($type)
                        ->where('options->date', $date);
                },
            ])
            ->get();

        return view('invite-overview', [
            'customers' => $customers,
            'type'      => $type,
            'date'      => $date,
        ]);
    }
}
