<?php

namespace App\Models;

use App\Modules\Team\Contracts\TeamAware;
use App\Modules\Team\Traits\TeamAwareTrait;
use App\Scopes\NotExpiredScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInvite extends Model implements TeamAware
{
    use HasFactory;
    use TeamAwareTrait;

    // https://medium.com/@alicenjoroge707/user-invitation-using-laravel-email-notifications-ef15197ba8e8
    protected $fillable = ['team_id', 'email', 'token', 'data', 'expires_at'];

    protected $casts = [
        'data'       => 'array',
        'expires_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new NotExpiredScope);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
