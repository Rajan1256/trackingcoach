<?php

namespace App\Models;

use App\Modules\Team\Contracts\TeamAware;
use App\Modules\Team\Traits\TeamAwareTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerSchedule extends Model implements TeamAware
{
    use HasFactory;
    use TeamAwareTrait;

    protected $fillable = [
        'user_id', 'tracker_id', 'team_id', 'time', 'day',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tracker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tracker_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
