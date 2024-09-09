<?php

namespace App\Exports\Sheets\BigData;

use App\Models\Supporter;

class SupportersSheet extends AbstractBigDataSheet
{
    public function query()
    {
        parent::query();
        $query = Supporter::query();

        if ($this->customer) {
            $query = $query->where('user_id', $this->customer->id);
        }

        return $query;
    }

    public function title(): string
    {
        return __('Supporters');
    }
}
