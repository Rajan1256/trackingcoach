<?php

namespace App\Exports\Sheets\BigData;

class PhysiologistsSheet extends AbstractBigDataSheet
{
    public function query()
    {
        parent::query();
        $query = current_team()
            ->users()
            ->isPhysiologists();

        if ($this->customer) {
            $query = $this->customer->physiologist();
        }

        return $query;
    }

    public function title(): string
    {
        return __('Physiologists');
    }
}
