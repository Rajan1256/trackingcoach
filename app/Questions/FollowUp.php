<?php

namespace App\Questions;

use App\Models\Answer;
use App\Models\Review;
use Illuminate\Support\Collection;

class FollowUp extends AbstractQuestion
{
    public static string $minimalPeriod = 'weekly';

    public static array $scaleTypes = [
        'followup' => [
            'did_not_follow_up',
            'did_a_little_follow_up',
            'did_some_follow_up',
            'did_regular_follow_up',
        ],
    ];

    protected string $savesTo = 'answer_number';

    protected bool $translatable = true;

    public static function storeRules(): array
    {
        return [
            'name'              => 'required',
            'options.scaleType' => 'required',
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
            'questions.followup.report.SinglePeriod',
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

        return view('questions.followup.report.SinglePeriod',
            compact('answers', 'review', 'answersSelf', 'answersOthers'))
            ->render();
    }

    public function getIcon(): ?string
    {
        return asset('img/questions/sevenscale.svg');
    }
}
