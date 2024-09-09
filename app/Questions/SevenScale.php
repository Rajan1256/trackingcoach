<?php

namespace App\Questions;

use App\Models\Answer;
use Illuminate\Support\Collection;

use function array_merge;

class SevenScale extends AbstractQuestion
{
    public static string $minimalPeriod = 'weekly';

    public static array $scaleTypes = [
        'greatness' => ['very_bad', 'average', 'very_good'],
        'happyness' => ['very_unhappy', 'average', 'very_happy'],
        'agree'     => ['strongly_disagree', 'neutral', 'strongly_agree'],
        'times'     => ['not_at_all', 'sometimes', 'always'],
    ];

    protected string $savesTo = 'answer_number';

    public static function storeRules(): array
    {
        return [
            'name'              => 'required|min:3',
            'options.start'     => 'required|between:1,7',
            'options.target'    => 'required|between:1,7',
            'options.scaleType' => 'required',
        ];
    }

    public function calculateWeeklyScore(Collection $answers): float|int|false
    {
        return $answers->map(function (Answer $answer) {
            return $this->calculateDailyScore($answer);
        })->average();
    }

    public function calculateDailyScore(Answer $answer): float|int|false
    {
        $given = intval($answer->{$this->savesTo});
        $options = $answer->questionHistory->options;

        $max = $options->get('target'); // somewhere between 1 and 7

        $matrix = $this->generateMatrix(1, $max);

        if (array_key_exists($given, $matrix)) {
            return $matrix[$given];
        }

        return false;
    }

    private function generateMatrix(int $min, int $max): array
    {
        $return = [];

        if ($min == $max && $min > 1) {
            $min = $min - 1;
        }
        if ($min == $max && $min == 1) {
            $max = $max + 1;
        }
        if ($min > $max) {
            $min = 1;
        }

        foreach (range(1, 7) as $i) {
            if ($i <= $min) {
                $return[$i] = 0;
            } elseif ($i >= $max) {
                $return[$i] = 100;
            } else {
                $return[$i] = 100 / ($max - $min) * ($i - $min);
            }
        }

        return $return;
    }

    public function displayAnswer(Answer $answer)
    {
        return intval($answer->{$this->savesTo});
    }

    public function formatOptions($options = []): array
    {
        return array_merge($options, [
            'start'  => intval($options['start'] ?? ''),
            'target' => intval($options['target'] ?? ''),
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
            'zero'       => null,
        ]);
    }

    public function getDailyGraphData(Answer $answer)
    {
        return collect([
            'answer_id' => $answer->id,
            'value'     => intval($answer->{$this->savesTo}),
            'start'     => $answer->questionHistory->options->get('start'),
            'target'    => $answer->questionHistory->options->get('target'),
            'zero'      => null,
        ]);
    }

    public function getDayTargetString(Answer $answer)
    {
        return $answer->questionHistory->options->get('target');
    }

    public function getIcon(): ?string
    {
        return asset('img/questions/sevenscale.svg');
    }
}
