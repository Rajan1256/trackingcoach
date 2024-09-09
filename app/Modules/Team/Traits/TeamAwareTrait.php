<?php

namespace App\Modules\Team\Traits;

use App\Modules\Team\Scopes\TeamAwareScope;

trait TeamAwareTrait
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    public static function bootTeamAwareTrait()
    {
        static::addGlobalScope(new TeamAwareScope());
    }
}
