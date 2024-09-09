<?php

namespace App\Modules\Team\Scopes;

use App\Enum\Roles;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TeamAwareScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  Builder  $builder
     * @param  Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if (current_team() && (current_team()->isRoot() &&
                Auth::user() &&
                Auth::user()->hasCurrentTeamRole([Roles::ADMIN]))) {
            $builder->where(function (Builder $query) use ($model) {
                $query->where($model->getTable().'.team_id', current_team()?->id)
                    ->orWhereNull($model->getTable().'.team_id');
            });
        } else {
            $builder->where($model->getTable().'.team_id', current_team()?->id);
        }
    }
}
