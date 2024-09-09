<?php

namespace App\Exports\Sheets\BigData;

use App\Models\Answer;

class TracklistQuestionsAnswersSheet extends AbstractBigDataSheet
{
    public function title(): string
    {
        return __('Tracklist questions answers');
    }

    public function query()
    {
        parent::query();
        return Answer::whereHas('question', function ($q) {
            $q->where('scope', 'tracklist')
                ->whereYear('created_at', $this->year);

            if ($this->customer) {
                $q->where('user_id', $this->customer->id);
            }
        });
    }
}
