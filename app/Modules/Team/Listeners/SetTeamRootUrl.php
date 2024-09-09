<?php

namespace App\Modules\Team\Listeners;

use App\Modules\Team\Events\SwitchedCurrentTeam;
use Illuminate\Support\Facades\URL;

class SetTeamRootUrl
{
    public function handle(SwitchedCurrentTeam $event)
    {
        $protocol = request()?->isSecure() || app()->environment('production') ? 'https' : 'http';
        $rootUrl = "$protocol://{$event->team->fqdn}";
        config()->set('app.url', $rootUrl);
        URL::forceRootUrl($rootUrl);
    }
}
