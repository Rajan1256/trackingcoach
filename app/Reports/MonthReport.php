<?php

namespace App\Reports;

use App\Models\MonthlyScore;
use App\Models\User;
use Carbon\Carbon;
use Jenssegers\Date\Date;

class MonthReport
{
    /**
     * @var User
     */
    public $customer;

    public $year;

    public $month;

    public $daysPerWeek;

    protected $scoresMonthlyCache;

    protected $scoresWeeklyCache;

    protected $scoresDailyCache;

    protected $answersCache;

    protected $questionsCache;

    protected $daysForMonth;

    protected $weeksForMonth;

    /**
     * MonthReport constructor.
     *
     * @param  User  $customer
     * @param $year
     * @param $month
     */
    public function __construct(User $customer, $year, $month)
    {
        $this->customer = $customer;
        $this->year = $year;
        $this->month = $month;

        $this->daysPerWeek = MonthlyScore::where('user_id', $this->customer->id)->where('month',
            $this->month)->where('year', $this->year)->max('days_per_week') == 7 ? 7 : 5;

        $this->fillCache();
    }

    private function fillCache()
    {
        $this->daysForMonth = $this->getDaysForMonth();
        $this->answersCache = $this->customer->answers()->tracklist()->with([
            'question', 'questionHistory',
        ])->between($this->daysForMonth->first(), $this->daysForMonth->last())->get();
        $this->weeksForMonth = $this->getWeeksForMonth();
        $this->scoresDailyCache = $this->customer->scores_daily()->between($this->daysForMonth->first(),
            $this->daysForMonth->last())->get();
        $this->scoresWeeklyCache = $this->getScoresWeeklyForCache();
        $this->questionsCache = $this->customer->questions()->tracklist()->withTrashed()->get();
        $this->scoresMonthlyCache = $this->customer->scores_monthly()->where('year', $this->year)->where('month',
            $this->month)->get()->sortBy(function ($score, $key) {
            return $this->questionsCache->where('id', $score->question_id)->first()->position;
        });
    }

    private function getDaysForMonth()
    {
        $start = Date::createFromDate($this->year, $this->month, 1)->startOfDay();
        $end = (new Date($start))->endOfMonth();

        $return = collect();

        while ($start < $end) {
            if ($start->isWeekday() || $this->daysPerWeek == 7) {
                $return->push((new Date($start)));
            }

            $start->addDay();
        }

        return $return->sortBy(function ($item) {
            return $item->timestamp;
        })->values();
    }

    private function getWeeksForMonth()
    {
        return $this->daysForMonth->filter(function (Carbon $day) {
            return $this->daysPerWeek == 7 ? $day->isSunday() : $day->isFriday();
        })->map(function ($lastWeekDay) {
            $lastWeekDay = (new Carbon($lastWeekDay));
            $monday = $lastWeekDay->modify('last monday');

            return [
                'year' => $monday->year,
                'week' => intval($monday->format('W')),
            ];
        })->values();
    }

    private function getScoresWeeklyForCache()
    {
        return $this->customer->scores_weekly()->where(function ($query) {
            foreach ($this->weeksForMonth as $weekArray) {
                $query->orWhere(function ($query) use ($weekArray) {
                    $query->where('week', $weekArray['week'])
                        ->where('year', $weekArray['year']);
                });
            }
        })->get();
    }

    public function getData()
    {
        return collect([
            'customer'       => $this->customer,
            'year'           => $this->year,
            'month'          => $this->month,
            'days'           => $this->daysForMonth,
            //
            'unique_days'    => $this->answersCache->unique('date')->count(),
            'response_ratio' => $this->answersCache->unique('date')->count() > 0 ? round(100 / $this->daysForMonth->count() * $this->answersCache->unique('date')->count()) : 0,
            'overall_score'  => $this->scoresMonthlyCache->average('score'),
            //
            'matrix'         => $this->getMatrix()->sortByDesc('score'),
        ]);
    }

    private function getMatrix()
    {
        return $this->scoresMonthlyCache->map(function ($item) {
            $daily = $this->scoresDailyCache->where('question_id', $item->question_id)->count() > 0;
            $answers = $this->answersCache->where('question_id', $item->question_id);
            $graphData = [];

            if ($daily) {
                $graphData[0] = [__('Date'), __('Value'), __('Target'), __('Start')];
                $i = 1;
                foreach ($this->scoresDailyCache->where('question_id', $item->question_id)->sortBy('date') as $ds) {
                    $graphData[$i] = [
                        date_format_helper($ds->date)->get_short_day_month(),
                        $ds->extra_data->get('value'),
                        $ds->extra_data->get('target'),
                        $ds->extra_data->get('start'),
                    ];
                    if ($ds->extra_data->get('zero')) {
                        if (count($graphData[0]) == 4) {
                            $graphData[0][] = __('Zero');
                        }

                        $graphData[$i][] = $ds->extra_data->get('zero');
                    }

                    $i++;
                }
            } else {
                $graphData[] = [__('Week'), __('Score')];
                foreach (
                    $this->scoresWeeklyCache->where('question_id', $item->question_id)->sortBy(function ($weekScore) {
                        return $weekScore->year.$weekScore->weeknumber;
                    }) as $ds
                ) {
                    $graphData[] = [
                        'Week '.$ds->weeknumber,
                        round($ds->score),
                    ];
                }
            }

            $scoreMonthlyRecord = $this->scoresMonthlyCache->where('question_id', $item->question_id)->first();

            if (!count($answers)) {
                return;
            }

            return [
                'question_id' => $item->question_id,
                'name'        => $answers->first()->questionHistory->name,
                'type'        => $daily ? 'daily' : 'weekly',
                'value'       => $scoreMonthlyRecord->extra_data->get('value'),
                'start'       => $scoreMonthlyRecord->extra_data->get('start'),
                'zero'        => $scoreMonthlyRecord->extra_data->get('zero'),
                'target'      => $scoreMonthlyRecord->extra_data->get('target'),
                'score'       => round($scoreMonthlyRecord->score),
                'graphData'   => $graphData,
            ];
        })->filter(function ($item) {
            return $item !== null;
        });
    }
}
