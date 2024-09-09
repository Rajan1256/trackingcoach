<?php

namespace App\Models;

use App\Modules\Team\Contracts\TeamAware;
use App\Modules\Team\Traits\TeamAwareTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PhysiologistUser extends Pivot implements TeamAware
{
    use HasFactory;
    use TeamAwareTrait;

    protected $fillable = [
        'team_id', 'user_id', 'physiologist_id',
    ];
}
