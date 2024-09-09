<?php

namespace App\Questions;

use App\Models\Answer;
use App\Models\Review;
use Illuminate\Support\Collection;

class Open extends AbstractQuestion
{
    public static bool $minimalPeriod = false;

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

    public function fetchReviewGraph($answers, Review $review)
    {
        $answersSelf = $answers->filter(function ($answer) {
            return $answer->supporter->relationship == 'self';
        });
        $answersOthers = $answers->filter(function ($answer) {
            return $answer->supporter->relationship != 'self';
        });

        return [
            'questions.open.report.SinglePeriod',
            compact('answers', 'review', 'answersSelf', 'answersOthers'),
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

        return view('questions.open.report.SinglePeriod',
            compact('answers', 'review', 'answersSelf', 'answersOthers'))
            ->render();
    }

    public function getIcon(): ?string
    {
        return asset('img/questions/open.svg');
    }

    protected function shouldContinue($answer)
    {
        if (strlen($answer) == 0) {
            return false;
        }

        return parent::shouldContinue($answer);
    }
}
