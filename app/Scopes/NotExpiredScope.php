<?php

namespace App\Scopes;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class NotExpiredScope implements Scope
{

    /**
     * @param  Builder  $builder
     * @param  Model  $model
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->where('expires_at', '>=', Carbon::now());
    }
}
