<?php

namespace App\Exports\Sheets\BigData;

use App\Models\Answer;

class ReviewQuestionsAnswersSheet extends AbstractBigDataSheet
{
    public function query()
    {
        parent::query();
        return Answer::whereHas('question', function ($q) {
            $q->where('scope', 'review')
                ->whereYear('created_at', $this->year);

            if ($this->customer) {
                $q->where('user_id', $this->customer->id);
            }
        });
    }

    public function title(): string
    {
        return __('Review questions answers');
    }
}
