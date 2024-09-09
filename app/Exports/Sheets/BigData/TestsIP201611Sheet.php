<?php

namespace App\Exports\Sheets\BigData;

use App\Models\Test;
use App\Topmind\Tests\IntroductionPerformance_201611;

class TestsIP201611Sheet extends AbstractBigDataSheet
{
    public function query()
    {
        parent::query();
        $query = Test::where('type', IntroductionPerformance_201611::class)
            ->whereYear('created_at', $this->year);

        if ($this->customer) {
            $query = $query->where('user_id', $this->customer->id);
        }

        return $query;
    }

    public function title(): string
    {
        return __('Test - Introduction Performance');
    }
}
