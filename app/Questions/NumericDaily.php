<?php

namespace App\Questions;

use App\Models\Answer;
use DivisionByZeroError;
use Illuminate\Support\Collection;

use function array_merge;

class NumericDaily extends AbstractQuestion
{
    public static string $dailyPeriod = 'daily';

    protected string $savesTo = 'answer_number';

    public static function storeRules(): array
    {
        return [
            'name'           => 'required',
            'options.start'  => 'required|regex:/[0-9]+[.,]?[0-9]*/',
            'options.target' => 'required|regex:/[0-9]+[.,]?[0-9]*/',
            'options.zero'   => 'required|regex:/[0-9]+[.,]?[0-9]*/|different:options.target',
        ];
    }

    public function getIcon(): ?string
    {
        return asset('img/questions/numeric.svg');
    }

    public function calculateWeeklyScore(Collection $answers): float|int|false
    {
        return $answers->map(function (Answer $answer) {
            return $this->calculateDailyScore($answer);
        })->average();
    }

    public function calculateDailyScore(Answer $answer): float|int|false
    {
        $given = $answer->{$this->savesTo};
        $options = $answer->questionHistory->options;

        if ($options->get('zero') == $given) {
            return 0;
        }

        try {
            return $this->normalizeScore(100 / ($options->get('target') - $options->get('zero')) * ($given - $options->get('zero')));
        } catch (DivisionByZeroError $divisionByZeroError) {
            return 100;
        }
    }

    public function displayAnswer(Answer $answer)
    {
        return floatval($answer->{$this->savesTo});
    }

    public function formatOptions($options = []): array
    {
        return array_merge($options, [
            'start'  => floatval(str_replace(',', '.', $options['start'] ?? '')),
            'target' => floatval(str_replace(',', '.', $options['target'] ?? '')),
            'zero'   => floatval(str_replace(',', '.', $options['zero'] ?? '')),
        ]);
    }

    public function generateWeeklyScoreData(Collection $answers)
    {
        return collect([
            'answer_ids' => $answers->pluck('id'),
            'value'      => $answers->average($this->savesTo),
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

    public function getDailyGraphData(Answer $answer)
    {
        return collect([
            'answer_id' => $answer->id,
            'value'     => floatval($answer->{$this->savesTo}),
            'start'     => $answer->questionHistory->options->get('start'),
            'target'    => $answer->questionHistory->options->get('target'),
            'zero'      => $answer->questionHistory->options->get('zero'),
        ]);
    }

    public function getDayTargetString(Answer $answer)
    {
        return $answer->questionHistory->options->get('target');
    }

    public function prepareAnswerForDb($answer)
    {
        return floatval(str_replace(',', '.', $answer));
    }
}
