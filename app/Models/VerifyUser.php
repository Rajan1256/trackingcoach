<?php

namespace App\Models;

use App\Modules\Team\Contracts\NotTeamAware;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VerifyUser extends Model implements NotTeamAware
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'token',
    ];
}
