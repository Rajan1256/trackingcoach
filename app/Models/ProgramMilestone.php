<?php

namespace App\Models;

use App\Modules\Team\Contracts\TeamAware;
use App\Modules\Team\Traits\TeamAwareTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramMilestone extends Model implements TeamAware
{
    use HasFactory;
    use TeamAwareTrait;

    protected $casts = ['date' => 'date'];

    protected $fillable = [
        'team_id', 'user_id', 'date', 'title',
    ];

    public function scopeSorted(Builder $query)
    {
        return $query->orderBy('date');
    }

    public function finished()
    {
        return $this->date < Carbon::now()->endOfDay();
    }
}
