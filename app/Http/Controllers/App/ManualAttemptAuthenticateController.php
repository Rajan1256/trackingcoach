<?php

namespace App\Http\Controllers\App;

use App\Models\User;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;

class ManualAttemptAuthenticateController
{
    public function __invoke(Request $request)
    {
        $fqdn = null;
        $found_user = null;
        $hasher = app('hash');
        $token = null;

        $user = User::where('email', $request->get('email'))->first();

        if ($user && $hasher->check($request->get('password'), $user->password)) {
            $team = $user->teams->first();
            $fqdn = $team->fqdn;
            $settings = $user->getSettings($team);

            $found_user = $user;
            if (!$settings->paired_app_token) {
                $settings->paired_app_token = md5(time().uniqid());
                $settings->save();
            }
            $token = $settings->paired_app_token;

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
        $data = [
            'token' => JWT::encode(
                [
                    'hostname' => sprintf('%s://%s', $protocol, $fqdn),
                    'user'     => optional($found_user)->toArray(),
                    'token'    => $token,
                ],
                md5($token)
            ),
        ];


        if (config('app.debug')) {
            $data['debug'] = [
                'hostname' => sprintf('%s://%s', $protocol, $fqdn),
                'user'     => optional($found_user)->toArray(),
                'token'    => $token,
            ];
        }

        return $data;
    }
}
