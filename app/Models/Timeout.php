<?php

namespace App\Models;

use App\Modules\Team\Contracts\TeamAware;
use App\Modules\Team\Traits\TeamAwareTrait;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Timeout extends Model implements TeamAware
{
    use HasFactory;
    use TeamAwareTrait;

    protected $fillable = ['user_id', 'team_id', 'start', 'end'];

    protected $casts = [
        'start' => 'date',
        'end'   => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeBetween($query, Carbon|CarbonImmutable $start, Carbon|CarbonImmutable $end)
    {
        $query->where('start', '<=', $end->format('Y-m-d'))
            ->where('end', '>=', $start->format('Y-m-d'));
    }

    public function isNow()
    {
        return $this->start->startOfDay() <= Carbon::now() && $this->end->endOfDay() >= Carbon::now();
    }
}
