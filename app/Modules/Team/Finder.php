<?php

namespace App\Modules\Team;

use App\Models\Team;
use Illuminate\Http\Request;

class Finder
{
    public function findForRequest(Request $request): ?Team
    {
        $host = $request->getHost();

        return Team::whereFqdn($host)->first();
    }
}
