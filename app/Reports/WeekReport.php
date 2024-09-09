<?php

namespace App\Reports;

use App\Models\Team;
use App\Models\User;
use App\Models\WeeklyScore;
use Carbon\CarbonImmutable;
use DateTime;
use Jenssegers\Date\Date;

class WeekReport
{
    protected static $scoresWeeklyCache;

    protected static $scoresDailyCache;

    protected static $answersCache;

    protected static $questionsCache;

    protected static $daysForWeek;

    public $customer;

    public $settings;

    public $year;

    public $week;

    public $daysPerWeek;

    public function __construct(User $customer, $year, $week, Team $team = null)
    {
        $this->settings = $customer->getSettings($team);
        $this->customer = $customer;
        $this->year = $year;
        $this->week = $week;
        $this->daysPerWeek = $this->customer->scores_weekly()->where('year', $this->year)->where('week',
            $this->week)->max('days_per_week') ?: $this->settings->days_per_week;

        $this->fillCache();
    }

    private function fillCache()
    {
        static::$daysForWeek = collect(range(1, $this->daysPerWeek))->map(function ($i) {
            $date = (new DateTime)->setISODate($this->year, $this->week, $i);

            return (new CarbonImmutable($date->getTimestamp()))->startOfDay();
        });

        static::$questionsCache = $this->customer->questions()->tracklist()->withTrashed()->get();
        static::$answersCache = $this->customer->answers()->tracklist()->with([
            'question', 'questionHistory',
        ])->between(static::$daysForWeek->first(), static::$daysForWeek->last()->endOfDay())->get();
        static::$scoresDailyCache = $this->customer->scores_daily()->between(static::$daysForWeek->first(),
            static::$daysForWeek->last()->endOfDay())->get();
        static::$scoresWeeklyCache = $this->customer->scores_weekly()->where('year', $this->year)->where('week',
            $this->week)->get()->sortBy(function ($score, $key) {
            return static::$questionsCache->where('id', $score->question_id)->first()->position;
        })->values();
    }

    public function getData()
    {
        return collect([
            'client'          => $this->customer,
            'year'            => $this->year,
            'week'            => $this->week,
            'days'            => static::$daysForWeek,
            //
            'days_per_week'   => $this->daysPerWeek,
            'unique_days'     => static::$answersCache->count() ? static::$answersCache->unique('date')->count() : 0,
            'overall_score'   => round(static::$scoresWeeklyCache->average('score')),
            'on_target'       => 1,
            //
            'daily_summary'   => $this->getDailySummary()->map(function ($score) {
                return round($score);
            }),
            //
            'weekly_timeline' => $this->getWeeklyTimelineData(),
            //
            'topsAndDips'     => $this->getTopsAndDips(),
        ]);
    }

    private function getDailySummary()
    {
        return static::$scoresDailyCache->groupBy(function ($weeklyScore) {
            return $weeklyScore->date->format('N');
        })->map(function ($perDay) {
            return $perDay->average('score');
        });
    }

    private function getWeeklyTimelineData()
    {
        return $this->customer->getLastWeeksGraphData(13, intval($this->year * 100) + intval($this->week));

        $timestamp = strtotime(date(strtotime($this->year.'W'.$this->week.'1')));

        return collect(range(0, 10))
            ->keyBy(function ($i) {
                return $i;
            })
            ->map(function ($i) use ($timestamp) {
                $t = (new Date($timestamp))->subWeeks($i);

                return [
                    'week'  => $t->format('W'),
                    'score' => $this->customer->scores_weekly()->where('year', $t->year)->where('week',
                        $t->format('W'))->get()->average('score'),
                ];
            })->map(function ($result) {
                $result['score'] = $result['score'] ? intval(round($result['score'])) : null;

                return $result;
            })->reverse()->values()->filter(function ($week) {
                return $week['score'] !== null;
            });
    }

    private function getTopsAndDips()
    {
        $collection = static::$scoresWeeklyCache->map(function (WeeklyScore $scoresWeekly) {
            $answers = static::$answersCache->where('question_id', $scoresWeekly->question_id);

            if (count($answers) == 0) {
                return;
            }

            return collect([
                'question_id'  => $scoresWeekly->question_id,
                'name'         => $answers->first()->questionHistory->name,
                'weekly_score' => round($scoresWeekly->score),
                'start'        => $scoresWeekly->extra_data->get('start'),
                'result'       => $this->formatResult($scoresWeekly->extra_data->get('value')),
                'target'       => $scoresWeekly->extra_data->get('target'),
                'days'         => static::$daysForWeek->map(function ($day) use ($scoresWeekly, $answers) {
                    $score = static::$scoresDailyCache
                        ->where('question_id', $scoresWeekly->question_id)
                        ->where('date', $day)
                        ->first();


                    $answer = $answers->where('date', $day)->first();

                    if (!$answer) {
                        return;
                    }

                    $type = $answer->questionHistory->type;
                    $className = 'App\\Questions\\'.$type;
                    $instance = (new $className);

                    return [
                        'date'   => $day,
                        'score'  => $score ? round($score->score) : null,
                        'answer' => $instance->displayAnswer($answer),
                        'target' => $instance->getDayTargetString($answer),
                    ];
                })->filter(function ($item) {
                    return $item !== null;
                })->sortBy('date'),
            ]);
        })->filter(function ($item) {
            return $item !== null;
        })->sortByDesc(function ($item) {
            // sort by descending score
            return $item->get('weekly_score');
        })->values();

//        dd ($collection);
        return $collection;
    }

    private function formatResult($result)
    {
        if (!$result) {
            return 0;
        }

        return round($result * 10) / 10;
    }
}
