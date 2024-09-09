<?php

namespace App\Exports\Sheets\BigData;

use App\Models\Answer;
use App\Models\Team;
use App\Modules\Team\Contracts\QueueTeamAware;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

use function array_keys;
use function is_null;

abstract class AbstractBigDataSheet implements FromQuery, WithTitle, ShouldQueue, QueueTeamAware, WithHeadings
{
    use Exportable;

    public $customerIds;

    public $supporterIds;

    public function __construct(public Team $team, public $year, public $customer = null)
    {
        $answers = Answer::groupBy('user_id')
            ->whereYear('created_at', $this->year);

        if (!is_null($customer)) {
            $answers = $answers->where('user_id', $customer->id);
        }

        $this->customerIds = $answers->get(['user_id'])
            ->pluck('user_id');

        $supportAnswers = Answer::groupBy('supporter_id')
            ->whereYear('created_at', $this->year);

        if (!is_null($customer)) {
            $supportAnswers = $supportAnswers->where('user_id', $customer->id);
        }

        $this->supporterIds = $supportAnswers->get(['supporter_id'])
            ->pluck('supporter_id');
    }

    abstract public function title(): string;

    public function headings(): array
    {
        return array_keys($this->query()->first()?->attributesToArray() ?? []);
    }

    public function query()
    {
        $this->team->makeCurrent();
    }
}
