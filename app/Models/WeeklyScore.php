<?php

namespace App\Models;

use App\Modules\Team\Contracts\TeamAware;
use App\Modules\Team\Traits\TeamAwareTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyScore extends Model implements TeamAware
{
    use HasFactory;
    use TeamAwareTrait;

    protected $casts = ['extra_data' => 'collection'];

    protected $fillable = [
        'team_id', 'user_id', 'days_per_week', 'question_id', 'question_history_id', 'score', 'week', 'year',
        'extra_data',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function questionHistory(): BelongsTo
    {
        return $this->belongsTo(QuestionHistory::class);
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
