<?php

namespace App\Listeners;

use App\Events\WeeklyScoresWereUpdated;
use App\Models\DailyScore;
use App\Models\MonthlyScore;
use App\Models\User;
use App\Models\WeeklyScore;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class RegenerateMonthlyScores
{
    protected ?Collection $weeksForMonth = null;

    /**
     * Handle the event.
     *
     * @param  WeeklyScoresWereUpdated  $event
     */
    public function handle(WeeklyScoresWereUpdated $event)
    {
        $start = (new Carbon($event->entry->date))->startOfMonth();
        $end   = (new Carbon($event->entry->date))->endOfMonth();

        // get all daily scores
        $dailyScores = DailyScore::between($start, $end)->whereUserId($event->entry->user_id)->get();

        // calculate and save monthly scores for questions with daily scores
        $dailyScores->groupBy('question_id')->each(function ($grouped, $question_id) use ($start, $event) {
            $score = $grouped->average('score');

            MonthlyScore::firstOrNew([
                'team_id'     => $grouped->first()->team_id,
                'user_id'     => $grouped->first()->user_id,
                'question_id' => $question_id,
                'month'       => $start->month,
                'year'        => $start->year,
            ])->fill([
                'days_per_week'       => $event->entry->user->days_per_week ?? 7, // @todo Jaco: FIX THIS
                'question_history_id' => $grouped->first()->question_history_id,
                'score'               => $score,
                'extra_data'          => $this->generateExtraDailyData($grouped),
            ])->save();
        });

        // save daily questions to array, so we can exclude these for the next loop
        $dailyScoreQuestionIds = $dailyScores->pluck('question_id')->unique();

        // get month for entry
        $month = (new Carbon($event->entry->date))->startOfWeek()->next(CarbonInterface::FRIDAY)->startOfMonth();
        $this->getWeeksForMonth($month->year, $month->month);

        $weeklyScores = $this->getWeeklyScore($event->entry->user, $dailyScoreQuestionIds);

        $weeklyScores->groupBy('question_id')->each(function ($grouped, $question_id) use ($start, $month, $event) {
            $score = $grouped->average('score');

            MonthlyScore::firstOrNew([
                'team_id'     => $grouped->first()->team_id,
                'user_id'     => $grouped->first()->user_id,
                'question_id' => $question_id,
                'month'       => $month->month,
                'year'        => $month->year,
            ])->fill([
                'days_per_week'       => $event->entry->user->days_per_week ?? 7, // @todo Jaco: FIX THIS
                'question_history_id' => $grouped->first()->question_history_id,
                'score'               => $score,
                'extra_data'          => $this->generateExtraWeeklyData($grouped),
            ])->save();
        });
    }

    private function generateExtraDailyData($grouped)
    {
        return collect([
            'answer_ids' => $grouped->map(function (DailyScore $daily) {
                return $daily->extra_data->get('answer_id');
            }),
            'value'      => $grouped->average(function (DailyScore $daily) {
                return $daily->extra_data->get('value');
            }),
            'start'      => $grouped->average(function (DailyScore $daily) {
                return $daily->extra_data->get('start');
            }),
            'target'     => $grouped->average(function (DailyScore $daily) {
                return $daily->extra_data->get('target');
            }),
            'zero'       => $grouped->filter(function (DailyScore $daily) {
                return $daily->extra_data->get('zero') !== null;
            })->average(function (DailyScore $daily) {
                return $daily->extra_data->get('zero');
            }),
        ]);
    }

    private function getWeeksForMonth($year, $month)
    {
        if ($this->weeksForMonth) {
            return $this->weeksForMonth;
        }

        $this->weeksForMonth = $this->getDaysForMonth($year, $month)->filter(function (Carbon $day) {
            return $day->isFriday();
        })->map(function ($friday) {
            $monday = $friday->modify('last monday');

            return [
                'year' => intval($monday->format('o')),
                'week' => intval($monday->format('W')),
            ];
        })->values();

        return $this->weeksForMonth;
    }

    private function getDaysForMonth($year, $month)
    {
        $start = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $end   = (new Carbon($start))->endOfMonth();

        $return = collect();

        while ($start < $end) {
            if ($start->isWeekday()) {
                $return->push((new Carbon($start)));
            }

            $start->addDay();
        }

        return $return;
    }

    private function getWeeklyScore(User $user, $excludeIds = [])
    {
        return WeeklyScore::where('user_id', $user->id)
            ->whereNotIn('question_id', $excludeIds)
            ->where(function ($query) {
                foreach ($this->weeksForMonth as $weekArray) {
                    $query->orWhere(function ($query) use ($weekArray) {
                        $query->where('week', $weekArray['week'])
                            ->where('year', $weekArray['year']);
                    });
                }
            })->get();
    }

    private function generateExtraWeeklyData($grouped)
    {
        return collect([
            'answer_ids' => $grouped->map(function (WeeklyScore $weekly) {
                return $weekly->extra_data->get('answer_ids');
            })->flatten(),
            'value'      => $grouped->average(function (WeeklyScore $weekly) {
                return $weekly->extra_data->get('value');
            }),
            'start'      => $grouped->average(function (WeeklyScore $weekly) {
                return $weekly->extra_data->get('start');
            }),
            'target'     => $grouped->average(function (WeeklyScore $weekly) {
                return $weekly->extra_data->get('target');
            }),
            'zero'       => $grouped->filter(function (WeeklyScore $weekly) {
                return $weekly->extra_data->get('zero') !== null;
            })->average(function (WeeklyScore $weekly) {
                return $weekly->extra_data->get('zero');
            }),
        ]);
    }
}
