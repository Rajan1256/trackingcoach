<?php

namespace App\Exports\Sheets\BigData;

use App\Models\WeeklyScore;

class WeeklyScoresSheet extends AbstractBigDataSheet
{
    public function query()
    {
        parent::query();
        $query = WeeklyScore::where('year', $this->year);

        if ($this->customer) {
            $query = $query->where('user_id', $this->customer->id);
        }

        return $query;
    }

    public function title(): string
    {
        return __('Weekly scores');
    }
}
