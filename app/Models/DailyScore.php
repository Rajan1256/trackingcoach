<?php

namespace App\Models;

use App\Modules\Team\Contracts\TeamAware;
use App\Modules\Team\Traits\TeamAwareTrait;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyScore extends Model implements TeamAware
{
    use HasFactory;
    use TeamAwareTrait;

    protected $casts = ['date' => 'date', 'extra_data' => 'collection'];

    protected $fillable = ['team_id', 'user_id', 'question_id', 'question_history_id', 'score', 'date', 'extra_data'];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function questionHistory(): BelongsTo
    {
        return $this->belongsTo(QuestionHistory::class);
    }

    public function scopeBetween(Builder $query, CarbonInterface $start, CarbonInterface $end): Builder
    {
        return $query->where('date', '>=', $start->format('Y-m-d'))
            ->where('date', '<=', $end->format('Y-m-d'));
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
