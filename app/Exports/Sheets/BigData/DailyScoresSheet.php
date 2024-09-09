<?php

namespace App\Exports\Sheets\BigData;

use App\Models\DailyScore;

class DailyScoresSheet extends AbstractBigDataSheet
{
    public function query()
    {
        parent::query();
        $query = DailyScore::whereYear('date', $this->year);

        if ($this->customer) {
            $query = $query->where('user_id', $this->customer->id);
        }

        return $query;
    }

    public function title(): string
    {
        return __('Daily scores');
    }
}
