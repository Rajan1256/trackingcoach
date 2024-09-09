<?php

namespace App\Http\Controllers\App;

use App\Models\Membership;
use App\Reports\MonthReport;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;

class MonthReportController
{
    public function __invoke(Request $request, $token, $year, $month)
    {
        $membership = Membership::where('paired_app_token', $token)->first();
        $settings = $membership->user->getSettings($membership->team);
        $user = $membership->user;
        $team = $membership->team;
        $team->makeCurrent();

        $data = (new MonthReport($user, $year, $month))->getData();

        return [
            'token' => JWT::encode(
                array_merge(['client_overall_score' => $user->getOverallScore()], $data->toArray()),
                md5($token)
            ),
        ];
    }
}
