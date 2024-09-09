<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Membership;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;

class RegisterDeviceController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  Request  $request
     * @return array
     */
    public function __invoke(Request $request, $token)
    {
        $this->validate($request, ['fcm_token' => 'required']);

        $membership = Membership::where('paired_app_token', $token)->firstOrFail();
        $user = $membership->user;
        $team = $membership->team;

        $team->makeCurrent();

        $fcm_token = $request->get('fcm_token');

        Device::withoutGlobalScopes()->updateOrCreate([
            'fcm_token' => $fcm_token,
        ], [
            'user_id' => $user->id,
            'team_id' => $team->id,
        ]);

        return [
            'token' => JWT::encode(
                ['success' => true],
                md5($token)
            ),
        ];
    }
}
