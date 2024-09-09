<?php

namespace App\Http\Controllers\App;

use App\Models\Membership;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;

class DashboardController
{
    public function __invoke(Request $request, string $token)
    {
        $membership = Membership::where('paired_app_token', $token)->first();
        $settings = $membership->user->getSettings($membership->team);
        $user = $membership->user;
        $team = $membership->team;
        $team->makeCurrent();
        $stats = $user->dashboardStatistics();

        $stats[4] = $stats[4]->map(function ($growth) {
            $growth->query = $growth->name;

            return $growth;
        });

        $stats[1] = $stats[1]->map(function ($item) {
            $item['weeknumber'] = $item['week'];
            return $item;
        });

        $stats[] = $team->getColors();
        $stats[] = $team->logo;
        $stats[] = $team->colorIsLight();
        $stats[] = $team->name;

        return [
            'token' => JWT::encode($stats, md5($token)),
        ];
    }
}
