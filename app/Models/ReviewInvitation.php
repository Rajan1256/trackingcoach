<?php

namespace App\Models;

use App\Modules\Team\Contracts\NotTeamAware;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewInvitation extends Model implements NotTeamAware
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'supporter_id', 'review_id', 'invite_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supporter()
    {
        return $this->belongsTo(Supporter::class);
    }

    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    public function invite()
    {
        return $this->belongsTo(Invite::class);
    }
}
