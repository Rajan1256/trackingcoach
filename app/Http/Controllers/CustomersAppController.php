<?php

namespace App\Http\Controllers;

use App\Enum\Roles;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Storage;

use function abort_if;
use function current_team;

class CustomersAppController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!current_team()->isRoot(), 403);
        abort_if(!Auth::user()->hasCurrentTeamRole([Roles::ADMIN]), 403);

        $customers = current_team()->users()->isCustomer()->get();

        foreach ($customers as $customer) {
            $settings = $customer->getSettings();

            if (!$settings->paired_app_token) {
                $settings->paired_app_token = md5(time().uniqid());
                $settings->save();
            }

            $customer->paired_app_token = $settings->paired_app_token;
        }

        return view('app.customers.index', [
            'customers' => $customers,
        ]);
    }

    public function show(Request $request, User $customer)
    {
        abort_if(!current_team()->isRoot(), 403);
        abort_if(!Auth::user()->hasCurrentTeamRole([Roles::ADMIN]), 403);
        abort_if(!current_team()->users->contains($customer), 403);

        $settings = $customer->getSettings();

        if (!$settings->paired_app_token) {
            $settings->paired_app_token = md5(time().uniqid());
            $settings->save();
        }

        Storage::disk('local')->put("tmp_qr_code_{$customer->id}.svg",
            QrCode::size(300)->generate($settings->paired_app_token));

        return response()->download(storage_path("app/tmp_qr_code_{$customer->id}.svg"),
            'qr_code_'.Str::snake($customer->name).'.svg')
            ->deleteFileAfterSend(true);
    }
}
