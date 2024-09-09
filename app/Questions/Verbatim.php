<?php

namespace App\Questions;

use App\Models\Answer;
use Illuminate\Support\Collection;

class Verbatim extends AbstractQuestion
{
    public static function storeRules(): array
    {
        return [
            'name' => 'required|min:3',
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

    public function getIcon(): ?string
    {
        return asset('img/questions/verbatim.svg');
    }

    protected function shouldContinue($answer)
    {
        if (strlen($answer) == 0) {
            return false;
        }

        return parent::shouldContinue($answer);
    }
}
