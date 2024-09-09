<?php

namespace App\Http\Controllers\App;

use App\Models\Membership;
use App\Reports\WeekReport;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;

class WeekReportController
{
    public function __invoke(Request $request, $token, $year, $week)
    {
        $membership = Membership::where('paired_app_token', $token)->first();
        $settings = $membership->user->getSettings($membership->team);
        $user = $membership->user;
        $team = $membership->team;
        $team->makeCurrent();

        $data = (new WeekReport($user, $year, $week))->getData();

        return [
            'token' => JWT::encode($data->toArray(), md5($token)),
        ];
    }
}
