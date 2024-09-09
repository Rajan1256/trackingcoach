<?php

namespace App\Exports\Sheets\BigData;

class CoachesSheet extends AbstractBigDataSheet
{
    public function query()
    {
        parent::query();
        $query = current_team()
            ->users()
            ->isCoach();

        if ($this->customer) {
            $query = $this->customer->coach();
        }

        return $query;
    }

    public function title(): string
    {
        return __('Coaches');
    }
}
