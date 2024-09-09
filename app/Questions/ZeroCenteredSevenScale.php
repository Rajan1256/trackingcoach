<?php

namespace App\Questions;

use App\Models\Answer;
use App\Models\Review;
use Illuminate\Support\Collection;

class ZeroCenteredSevenScale extends AbstractQuestion
{
    public static string $minimalPeriod = 'weekly';

    public static array $scaleTypes = [
        'effectiveness' => [
            'much_less_effective',
            'noticeably_less_effective',
            'little_less_effective',
            'no_change',
            'little_more_effective',
            'noticeably_more_effective',
            'much_more_effective',
        ],
        'agree'         => [
            'strongly_disagree',
            'disagree',
            'somewhat_disagree',
            'neutral',
            'somewhat_agree',
            'agree',
            'strongly_agree',
        ],
        'times'         => [
            'not_at_all',
            'rarely',
            'occasionally',
            'sometimes',
            'frequently',
            'often',
            'always',
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

    public function displayAnswer(Answer $answer)
    {
        $scaleType = $answer->questionHistory->options->get('scaleType', 'effectiveness');

        return trans('trackingcoach.questions.options.'.static::$scaleTypes[$scaleType][intval($answer->{$this->savesTo}) + 3]);
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
            'questions.zerocenteredsevenscale.report.SinglePeriod',
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

        return view('questions.zerocenteredsevenscale.report.SinglePeriod',
            compact('answers', 'review', 'answersSelf', 'answersOthers'))
            ->render();
    }

    public function getIcon(): ?string
    {
        return asset('img/questions/sevenscale.svg');
    }
}
