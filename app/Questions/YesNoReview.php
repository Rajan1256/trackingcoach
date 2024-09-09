<?php

namespace App\Questions;

use App\Models\Answer;
use App\Models\Review;
use Illuminate\Support\Collection;

class YesNoReview extends AbstractQuestion
{
    public static string $minimalPeriod = 'weekly';

    protected string $savesTo = 'answer_boolean';

    protected bool $translatable = true;

    public static function storeRules(): array
    {
        return [
            'name' => 'required',
        ];
    }

    public function calculateDailyScore(Answer $answer): float|int|false
    {
        return false;
    }

    public function calculateWeeklyScore(Collection $answers): float|int|false
    {
        return false;
    }

    public function displayAnswer(Answer $answer)
    {
        return $answer->{$this->savesTo} ? trans('trackingcoach.questions.options.yes') : trans('trackingcoach.questions.options.no');
    }

    public function fetchReviewGraph($answers, Review $review)
    {
        $answersSelf = $answers->filter(function ($answer) {
            return $answer->supporter->relationship == 'self';
        });
        $answersOthers = $answers->filter(function ($answer) {
            return $answer->supporter->relationship != 'self';
        });

        return [
            'questions.yesnoreview.report.SinglePeriod', compact('answers', 'review', 'answersSelf', 'answersOthers'),
        ];
    }

    public function generateReviewGraph($answers, Review $review)
    {
        $answersSelf = $answers->filter(function ($answer) {
            return $answer->supporter->relationship == 'self';
        });
        $answersOthers = $answers->filter(function ($answer) {
            return $answer->supporter->relationship != 'self';
        });

        return view('questions.yesnoreview.report.SinglePeriod',
            compact('answers', 'review', 'answersSelf', 'answersOthers'))
            ->render();
    }

    public function getIcon(): ?string
    {
        return asset('img/questions/boolean.svg');
    }
}
