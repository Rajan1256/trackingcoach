<?php

namespace App\Http\Controllers\App;

use App\Models\Membership;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;

class AttemptAuthenticateController
{
    public function __invoke(Request $request, string $token)
    {
        $fqdn = null;
        $found_user = null;

        $membership = Membership::where('paired_app_token', $token)->firstOrFail();
        $settings = $membership->user->getSettings($membership->team);
        $user = $membership->user;
        $team = $membership->team;
        $team->makeCurrent();

        if ($user) {
            $fqdn = $team->fqdn;

            $found_user = $user;

            if (!$settings->paired_app_on) {
                $settings->paired_app_on = Carbon::now();
                $settings->preferred_notification_methods = [
                    'daily_invites'   => 'app',
                    'weekly_reports'  => 'app',
                    'monthly_reports' => 'app',
                ];
                $settings->save();
            }
        }


        $protocol = config('app.env') === 'local' ? 'http' : 'https';

        return [
            'token' => JWT::encode(
                [
                    'hostname' => sprintf('%s://%s', $protocol, $fqdn),
                    'user'     => optional($found_user)->toArray(),
                ],
                md5($token)
            ),
        ];
    }
}
