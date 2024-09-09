<?php

namespace App\Questions;

use App\Models\Answer;
use Illuminate\Support\Collection;

class NumericWeekly extends AbstractQuestion
{
    /**
     * Array of generationMethods. Key will be used for storing in database
     * and defining functions, value will be used for translations.
     *
     * @var array
     */
    public static array $generationMethods = [
        'sumValues'    => 'trackingcoach.questions.generationMethods.sumValues',
        'averageValue' => 'trackingcoach.questions.generationMethods.averageValue',
        'lowestValue'  => 'trackingcoach.questions.generationMethods.lowestValue',
        'highestValue' => 'trackingcoach.questions.generationMethods.highestValue',
    ];

    public static string $minimalPeriod = 'weekly';

    protected string $savesTo = 'answer_number';

    public static function storeRules(): array
    {
        return [
            'name'                            => 'required',
            'options.start'                   => 'required|regex:/[0-9]+[.,]?[0-9]*/',
            'options.target'                  => 'required|regex:/[0-9]+[.,]?[0-9]*/',
            'options.zero'                    => 'required|regex:/[0-9]+[.,]?[0-9]*/',
            'options.score_generation_method' => 'required',
        ];
    }

    public function calculateDailyScore(Answer $answer): float|int|false
    {
        return false;
    }

    public function calculateWeeklyScore(Collection $answers): float|int|false
    {
        $options = $answers->first()->questionHistory->options;
        $scoreGenerationMethod = 'calculateWeekly'.ucfirst($options->get('score_generation_method'));

        return call_user_func([$this, $scoreGenerationMethod], $answers, $options);
    }

    public function calculateWeeklySumValues(Collection $answers, Collection $options)
    {
        $sum = $answers->sum($this->savesTo);

        if ($sum == $options->get('zero')) {
            return 0;
        }

        $r = ($options->get('target') - $options->get('zero'));
        $score = ($r != 0 ? 100 / $r : 0) * ($sum - $options->get('zero'));

        return $this->normalizeScore($score);
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
        $options = $answers->first()->questionHistory->options;

        $value = match ($options->get('score_generation_method')) {
            'sumValues' => $answers->sum($this->savesTo),
            'averageValue' => $answers->average($this->savesTo),
            'lowestValue' => $answers->min($this->savesTo),
            'highestValue' => $answers->max($this->savesTo),
            default => 0
        };

        return collect([
            'answer_ids' => $answers->pluck('id'),
            'value'      => $value,
            'start'      => $options->get('start'),
            'target'     => $options->get('target'),
            'zero'       => $options->get('zero'),
        ]);
    }

    public function getDayTargetString(Answer $answer)
    {
        $options = $answer->questionHistory->options;

        return match ($options->get('score_generation_method')) {
            'sumValues' => $options->get('target').'/'.trans('general.week'),
            'averageValue' => trans('general.averageOf').' '.$options->get('target'),
            'lowestValue' => trans('general.minimumOf').' '.$options->get('target'),
            'highestValue' => trans('general.maximumOf').' '.$options->get('target')
        };
    }

    public function getIcon(): ?string
    {
        return asset('img/questions/numeric.svg');
    }

    public function prepareAnswerForDb($answer)
    {
        return floatval(str_replace(',', '.', $answer));
    }

    private function calculateWeeklyAverageValue(Collection $answers, Collection $options)
    {
        $average = $answers->average($this->savesTo);

        if ($average == $options->get('zero')) {
            return 0;
        }

        $score = 100 / ($options->get('target') - $options->get('zero')) * ($average - $options->get('zero'));

        return $this->normalizeScore($score);
    }

    private function calculateWeeklyHighestValue(Collection $answers, Collection $options)
    {
        $highest = $answers->max($this->savesTo);

        if ($highest == $options->get('zero')) {
            return 0;
        }

        $score = 100 / ($options->get('target') - $options->get('zero')) * ($highest - $options->get('zero'));

        return $this->normalizeScore($score);
    }

    private function calculateWeeklyLowestValue(Collection $answers, Collection $options)
    {
        $lowest = $answers->min($this->savesTo);

        if ($lowest == $options->get('zero')) {
            return 0;
        }

        $score = 100 / ($options->get('target') - $options->get('zero')) * ($lowest - $options->get('zero'));

        return $this->normalizeScore($score);
    }
}
