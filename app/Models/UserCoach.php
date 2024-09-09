<?php

namespace App\Models;

use App\Modules\Team\Contracts\TeamAware;
use App\Modules\Team\Traits\TeamAwareTrait;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserCoach extends Pivot implements TeamAware
{
    use TeamAwareTrait;
    
    protected $table = 'coach_user';
}
