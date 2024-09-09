<?php

namespace App\Questions;

use App\Models\Answer;
use Illuminate\Support\Collection;

class YesNo extends AbstractQuestion
{
    public static string $minimalPeriod = 'weekly';

    protected string $savesTo = 'answer_boolean';

    public static function storeRules(): array
    {
        return [
            'name'             => 'required|min:3',
            'options.positive' => 'required',
            'options.start'    => 'required|between:0,7',
            'options.target'   => 'required|between:0,7',
        ];
    }

    public function calculateDailyScore(Answer $answer): float|int|false
    {
        return false;
    }

    public function calculateWeeklyScore(Collection $answers): float|int|false
    {
        $options = $answers->first()->questionHistory->options;

        $count = $answers->where($this->savesTo, $options->get('positive'))->count();

        if ($count == 0) {
            return 0;
        }

        $score = 100 / $options->get('target') * $count;

        return $this->normalizeScore($score);
    }

    public function displayAnswer(Answer $answer)
    {
        return $answer->{$this->savesTo} ? trans('trackingcoach.questions.options.yes') : trans('trackingcoach.questions.options.no');
    }

    public function formatOptions($options = []): array
    {
        return array_merge($options, [
            'positive' => (bool) intval($options['positive'] ?? ''),
            'start'    => intval($options['start'] ?? ''),
            'target'   => intval($options['target'] ?? ''),
        ]);
    }

    public function generateWeeklyScoreData(Collection $answers)
    {
        return collect([
            'answer_ids' => $answers->pluck('id'),
            'value'      => $answers->sum(function ($answer) {
                return boolval($answer->{$this->savesTo}) == $answer->questionHistory->options->get('positive') ? 1 : 0;
            }),
            'start'      => $answers->map(function (Answer $answer) {
                return $answer->questionHistory->options->get('start');
            })->average(),
            'target'     => $answers->map(function (Answer $answer) {
                return $answer->questionHistory->options->get('target');
            })->average(),
            'zero'       => $answers->map(function (Answer $answer) {
                return $answer->questionHistory->options->get('zero');
            })->average(),
        ]);
    }

    public function getDayTargetString(Answer $answer)
    {
        $options = $answer->questionHistory->options;

        return $options->get('target').'x '.($options->get('positive') ? trans('trackingcoach.questions.options.yes') : trans('trackingcoach.questions.options.no')).'/week';
    }

    public function getIcon(): ?string
    {
        return asset('img/questions/boolean.svg');
    }
}
