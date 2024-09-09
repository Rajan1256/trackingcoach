<?php

namespace App\Exports\Sheets\BigData;

use App\Models\MonthlyScore;

class MonthlyScoresSheet extends AbstractBigDataSheet
{
    public function query()
    {
        parent::query();
        $query = MonthlyScore::where('year', $this->year);

        if ($this->customer) {
            $query = $query->where('user_id', $this->customer->id);
        }

        return $query;
    }

    public function title(): string
    {
        return __('Monthly scores');
    }
}
