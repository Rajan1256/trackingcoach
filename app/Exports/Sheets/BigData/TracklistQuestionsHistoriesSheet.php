<?php

namespace App\Exports\Sheets\BigData;

use App\Models\QuestionHistory;

class TracklistQuestionsHistoriesSheet extends AbstractBigDataSheet
{
    public function query()
    {
        parent::query();
        return QuestionHistory::whereHas('question', function ($q) {
            $q->where('scope', 'tracklist')
                ->whereYear('created_at', $this->year);

            if ($this->customer) {
                $q->where('user_id', $this->customer->id);
            }
        });
    }

    public function title(): string
    {
        return __('Tracklist questions revisions');
    }
}
