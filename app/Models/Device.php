<?php

namespace App\Models;

use App\Modules\Team\Contracts\NotTeamAware;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model implements NotTeamAware
{
    use HasFactory;

    protected $fillable = [
        'team_id', 'user_id', 'fcm_token', 'info',
    ];

    protected $casts = [
        'info' => 'encrypted:array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
