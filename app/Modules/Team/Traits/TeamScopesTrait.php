<?php

namespace App\Modules\Team\Traits;

use Illuminate\Database\Eloquent\Builder;

trait TeamScopesTrait
{
    public function scopeForCurrentTeam(Builder $query)
    {
        return $query->where('team_id', current_team()?->id);
    }
}
