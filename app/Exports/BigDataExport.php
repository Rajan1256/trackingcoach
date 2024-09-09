<?php

namespace App\Exports;

use App\Exceptions\MissingExportsSheetException;
use App\Models\Team;
use App\Modules\Team\Contracts\QueueTeamAware;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Str;

use function current_team;
use function throw_if;

class BigDataExport implements WithMultipleSheets, ShouldQueue, QueueTeamAware
{
    use Exportable;

    public Team $team;

    public $year;

    public $customer;

    private $sheetNames = [
        'customers',
        'coaches',
        'physiologists',
        'timeouts',
        'notes',
//        'testsIP201611',
//        'testsCP201611',
        'interviews',
        'reviews',
        'reviewGoals',
        'supporters',
        'dailyScores',
        'weeklyScores',
        'monthlyScores',
        'tracklistQuestions',
        'tracklistQuestionsHistories',
        'tracklistQuestionsAnswers',
        'reviewQuestions',
        'reviewQuestionsHistories',
        'reviewQuestionsAnswers',
    ];

    public function __construct($year, $customer = null)
    {
        $this->team = current_team();
        $this->year = $year;
        $this->customer = $customer;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->sheetNames as $className) {
            $namespacedClassName = 'App\\Exports\\Sheets\\BigData\\'.Str::ucfirst($className).'Sheet';

            throw_if(
                !class_exists($namespacedClassName),
                MissingExportsSheetException::class,
                $namespacedClassName.' does not exist.'
            );

            $sheets[] = new $namespacedClassName($this->team, $this->year, $this->customer);
        }

        return $sheets;
    }
}
