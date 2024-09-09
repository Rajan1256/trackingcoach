<?php

namespace App\Exports\Sheets\BigData;

use App\Models\Question;

class ReviewQuestionsSheet extends AbstractBigDataSheet
{
    public function query()
    {
        parent::query();
        $query = Question::review()
            ->whereYear('created_at', $this->year);

        if ($this->customer) {
            $query = $query->where('user_id', $this->customer->id);
        }

        return $query;
    }

    public function title(): string
    {
        return __('Review questions');
    }
}
