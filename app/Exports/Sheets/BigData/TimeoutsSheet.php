<?php

namespace App\Exports\Sheets\BigData;

use App\Models\Timeout;

class TimeoutsSheet extends AbstractBigDataSheet
{
    public function query()
    {
        parent::query();
        $query = Timeout::where(function ($query) {
            $query->whereYear('start', $this->year)
                ->orWhereYear('end', $this->year);
        });

        if ($this->customer) {
            $query = $query->where('user_id', $this->customer->id);
        }

        return $query;
    }

    public function title(): string
    {
        return __('Timeouts');
    }
}
