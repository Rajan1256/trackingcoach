<?php

namespace App\Models;

use App\Modules\Team\Contracts\TeamAware;
use App\Modules\Team\Traits\TeamAwareTrait;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model implements TeamAware
{
    use HasFactory;
    use TeamAwareTrait;

    protected $casts = ['answer_boolean' => 'bool', 'answer_number' => 'decimal:4', 'date' => 'date'];

    protected $fillable = [
        'scope', 'user_id', 'supporter_id', 'review_id', 'question_id', 'question_history_id', 'answer_boolean',
        'answer_number', 'answer_text', 'date', 'team_id',
    ];

    public static function store(array $answers, User $customer, Carbon $date)
    {
        foreach ($answers as $question_history_id => $answer) {
            $qh = QuestionHistory::with('question')->find($question_history_id);
            $className = 'App\\Questions\\'.$qh->type;

            (new $className)->storeAnswer($customer, $answer, $qh, $date);
        }
    }

    public static function storeReview(array $answers, Supporter $supporter = null, Review $review, Carbon $date)
    {
        foreach ($answers as $question_history_id => $answer) {
            $qh = QuestionHistory::with('question')->find($question_history_id);
            $className = 'App\\Questions\\'.$qh->type;

            (new $className)->storeReviewAnswer($supporter, $review, $answer, $qh, $date);
        }
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id')
            ->withTrashed();
    }

    public function questionHistory(): BelongsTo
    {
        return $this->belongsTo(QuestionHistory::class, 'question_history_id');
    }

    public function review(): ?BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    public function scopeBetween($query, CarbonInterface $start, CarbonInterface $end)
    {
        return $query->where('date', '>=', $start->format('Y-m-d'))
            ->where('date', '<=', $end->format('Y-m-d'));
    }

    public function scopeReview($query)
    {
        return $query->where('answers.scope', 'review');
    }

    public function scopeTracklist($query)
    {
        return $query->where('answers.scope', 'tracklist');
    }

    public function supporter(): ?BelongsTo
    {
        return $this->belongsTo(Supporter::class)
            ->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
