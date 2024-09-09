<?php

namespace App\Reports;

use App\Answer;
use App\Client;
use App\QuestionHistory;
use App\ScoresDaily;
use App\ScoresMonthly;
use App\ScoresWeekly;
use Carbon\Carbon;

class ClientReport
{
    /**
     * @var Client
     */
    public $client;
    public $questions;
    /**
     * @var Carbon
     */
    private $from;
    /**
     * @var Carbon
     */
    private $to;

    public function __construct(Client $client, Carbon $from, Carbon $to)
    {
        $this->client = $client;
        $this->questions = $client->questions()->tracklist()->with('history')->get();
        $this->from = $from;
        $this->to = $to;
    }

    public function overviewData()
    {
        return collect([
            'client' => $this->client->user ? $this->client->user->name : $this->client->id,
            'from' => $this->from,
            'to' => $this->to,
        ]);
    }

    public function verbatimData()
    {
//        $verbatimIds = QuestionHistory::whereType('Verbatim')->whereClientId($this->client->id)->get()->pluck('question_id');
        $verbatimIds = $this->questions->filter(function ($question) {
            return $question->type == 'Verbatim';
        })->pluck('id');

        if (! $verbatimIds->count()) {
            return collect();
        }

        return $this->client->answers()
            ->whereIn('question_id', $verbatimIds)
            ->where('date', '>=', $this->from->format('Y-m-d'))
            ->where('date', '<=', $this->to->format('Y-m-d'))
            ->orderBy('date', 'asc')
            ->get()->map(function (Answer $answer) {
                $answer->date = (new Carbon($answer->date));

                return $answer;
            });
    }

    public function weekscoresData()
    {
        $from = (new Carbon($this->from))->startOfWeek();
        $to = (new Carbon($this->to))->startOfWeek();

        $weeksCollection = collect();

        while ($from <= $to) {
            $weeksCollection->push([
                'week' => $from->weekOfYear,
                'year' => $from->year,
            ]);
            $from->addWeek();
        }

        if ($weeksCollection->count() == 0) {
            return collect();
        }

        return $this->client->scores_weekly()->with('questionHistory')->where(function ($query) use ($weeksCollection) {
            foreach ($weeksCollection as $weekArray) {
                $query->orWhere(function ($query) use ($weekArray) {
                    $query->where('weeknumber', $weekArray['week'])
                        ->where('year', $weekArray['year']);
                });
            }
        })->get()->map(function (ScoresWeekly $scoresWeekly) {
            return [
                'question_id' => $scoresWeekly->question_id,
                'question_version' => $scoresWeekly->questionHistory->id,
                'question' => $scoresWeekly->questionHistory->name,
                'score' => $scoresWeekly->score,
                'week' => $scoresWeekly->weeknumber,
                'year' => $scoresWeekly->year,
                'value' => $scoresWeekly->extra_data->get('value'),
                'start' => $scoresWeekly->extra_data->get('start'),
                'target' => $scoresWeekly->extra_data->get('target'),
                'zero' => $scoresWeekly->extra_data->get('zero'),
                'answers' => count($scoresWeekly->extra_data->get('answer_ids', [])),
            ];
        });
    }

    public function monthscoresData()
    {
        $from = (new Carbon($this->from))->startOfMonth();
        $to = (new Carbon($this->to))->startOfMonth();

        $monthsCollection = collect();

        while ($from <= $to) {
            $monthsCollection->push([
                'month' => $from->month,
                'year' => $from->year,
            ]);
            $from->addMonth();
        }

        if ($monthsCollection->count() == 0) {
            return collect();
        }

        return $this->client->scores_monthly()->with('questionHistory')->where(function ($query) use ($monthsCollection) {
            foreach ($monthsCollection as $monthArray) {
                $query->orWhere(function ($query) use ($monthArray) {
                    $query->where('month', $monthArray['month'])
                        ->where('year', $monthArray['year']);
                });
            }
        })->get()->map(function (ScoresMonthly $scoresMonthly) {
            return [
                'question_id' => $scoresMonthly->question_id,
                'question_version' => $scoresMonthly->questionHistory->id,
                'question' => $scoresMonthly->questionHistory->name,
                'score' => $scoresMonthly->score,
                'month' => $scoresMonthly->month,
                'year' => $scoresMonthly->year,
                'value' => $scoresMonthly->extra_data->get('value'),
                'start' => $scoresMonthly->extra_data->get('start'),
                'target' => $scoresMonthly->extra_data->get('target'),
                'zero' => $scoresMonthly->extra_data->get('zero'),
                'answers' => count($scoresMonthly->extra_data->get('answer_ids', [])),
            ];
        });
    }

    public function dayscoresData()
    {
        $from = (new Carbon($this->from))->startOfDay();
        $to = (new Carbon($this->to))->startOfDay();

        $dayCollection = collect();

        while ($from <= $to) {
            $dayCollection->push($from->format('Y-m-d'));
            $from->addDay();
        }

        if ($dayCollection->count() == 0) {
            return collect();
        }

        return $this->client->scores_daily()->with('questionHistory')->where(function ($query) use ($dayCollection) {
            foreach ($dayCollection as $day) {
                $query->orWhere('date', $day);
            }
        })->get()->map(function (ScoresDaily $scoreDaily) {
            return [
                'question_id' => $scoreDaily->question_id,
                'question_version' => $scoreDaily->questionHistory->id,
                'question' => $scoreDaily->questionHistory->name,
                'score' => $scoreDaily->score,
                'date' => $scoreDaily->date,
                'value' => $scoreDaily->extra_data->get('value'),
                'start' => $scoreDaily->extra_data->get('start'),
                'target' => $scoreDaily->extra_data->get('target'),
                'zero' => $scoreDaily->extra_data->get('zero'),
            ];
        });
    }

    public function answersData()
    {
        $verbatimIds = $this->questions->filter(function ($question) {
            return $question->type == 'Verbatim';
        })->pluck('id');

        return $this->client->answers()->tracklist()
            ->whereNotIn('question_id', $verbatimIds)
            ->with('questionHistory')
            ->where('date', '>=', $this->from->format('Y-m-d'))
            ->where('date', '<=', $this->to->format('Y-m-d'))
            ->orderBy('date', 'asc')
            ->get()->map(function (Answer $answer) {
                $answer->date = (new Carbon($answer->date));

                return $answer;
            })->map(function (Answer $answer) {
                $className = '\\App\\Questions\\'.$answer->questionHistory->type;
                $savesTo = $className::dbColumn();

                return [
                    'question_id' => $answer->question_id,
                    'question_version' => $answer->questionHistory->id,
                    'question' => $answer->questionHistory->name,
                    'date' => $answer->date,
                    'value' => $savesTo == 'answer_boolean' ? yesNoVal($answer->$savesTo) : $answer->$savesTo,
                ];
            });
    }
}
