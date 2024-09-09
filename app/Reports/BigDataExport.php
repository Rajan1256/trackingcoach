<?php

namespace App\Reports;

use App\Answer;
use App\Client;
use App\Coach;
use App\Goal;
use App\Note;
use App\Physiologist;
use App\Question;
use App\QuestionHistory;
use App\Review;
use App\ScoresDaily;
use App\ScoresMonthly;
use App\ScoresWeekly;
use App\Stakeholder;
use App\Talk;
use App\Test;
use App\Timeout;
use App\Topmind\Tests\ConcludingPerformance_201611;
use App\Topmind\Tests\IntroductionPerformance_201611;
use App\Tracker;
use App\TrackingLog;
use Carbon\Carbon;
use Illuminate\Support\Str;

class BigDataExport
{
    public $coachIds;

    /**
     * @var Carbon
     */
    private $from;

    /**
     * @var Carbon
     */
    private $to;

    public function __construct(Carbon $from, Carbon $to)
    {
        $this->from = $from;
        $this->to = $to->endOfDay();

        $this->clientIds = Answer::groupBy('client_id')
            ->where('created_at', '>', $this->from)
            ->where('created_at', '<', $this->to)
            ->get(['client_id'])
            ->pluck('client_id');

        $this->stakeholderIds = Answer::groupBy('stakeholder_id')
            ->review()
            ->where('created_at', '>', $this->from)
            ->where('created_at', '<', $this->to)
            ->get(['stakeholder_id'])
            ->pluck('stakeholder_id');

        $this->trackerIds = TrackingLog::groupBy('tracker_id')
            ->where('created_at', '>', $this->from)
            ->where('created_at', '<', $this->to)
            ->get(['tracker_id'])
            ->pluck('tracker_id');

        $this->coachIds = Coach::where('created_at', '<=', $this->to)->get(['id'])->pluck('id');
        $this->physiologistIds = Physiologist::where('created_at', '<=', $this->to)->get(['id'])->pluck('id');
    }

    public function clientsData()
    {
        $clients = Client::with('users')->whereIn('id', $this->clientIds)->withTrashed()->get();

        return collect([
            'clients' => $clients,
        ]);
    }

    public function coachesData()
    {
        $coaches = Coach::with('users')->whereIn('id', $this->coachIds)->get();

        return collect([
            'coaches' => $coaches,
        ]);
    }

    public function physiologistsData()
    {
        $physiologists = Physiologist::with('users')->whereIn('id', $this->physiologistIds)->get();

        return collect([
            'physiologists' => $physiologists,
        ]);
    }

    public function trackersData()
    {
        $trackers = Tracker::with('users')->whereIn('id', $this->trackerIds)->get();

        return collect([
            'trackers' => $trackers,
        ]);
    }

    public function trackingLogs()
    {
        $logs = TrackingLog::where('created_at', '>', $this->from)
            ->where('created_at', '<', $this->to->endOfDay())
            ->get()
            ->groupBy(function (TrackingLog $log, $key) {
                return "{$log->client_id}-{$log->date}-{$log->tracker_id}";
            });

        return collect([
            'logs' => $logs,
        ]);
    }

    public function questions()
    {
        $questionIds = Answer::groupBy('question_id')
            ->tracklist()
            ->where('date', '>', $this->from)
            ->where('date', '<=', $this->to)
            ->get(['question_id'])
            ->pluck('question_id');

        $questions = Question::whereIn('id', $questionIds)
            ->with('history')
            ->get();

        return collect([
            'questions' => $questions,
        ]);
    }

    public function questionRevisions()
    {
        $questionHistoryIds = Answer::groupBy('question_history_id')
            ->tracklist()
            ->where('date', '>', $this->from)
            ->where('date', '<=', $this->to)
            ->get(['question_history_id'])
            ->pluck('question_history_id');

        $questionRevisions = QuestionHistory::whereIn('id', $questionHistoryIds)
            ->get();

        return collect([
            'revisions' => $questionRevisions,
        ]);
    }

    public function answers()
    {
        $answers = Answer::tracklist()
            ->between($this->from, $this->to)
            ->get();

        return collect([
            'answers' => $answers,
        ]);
    }

    public function dailyScores()
    {
        $scores = ScoresDaily::between($this->from, $this->to)
            ->get();

        return collect([
            'scores' => $scores,
        ]);
    }

    public function weeklyScores()
    {
        $scores = ScoresWeekly::between($this->from, $this->to)
            ->get();

        return collect([
            'scores' => $scores,
        ]);
    }

    public function monthlyScores()
    {
        $scores = ScoresMonthly::between($this->from, $this->to)
            ->get();

        return collect([
            'scores' => $scores,
        ]);
    }

    public function timeouts()
    {
        $timeouts = Timeout::between($this->from, $this->to)
            ->orderBy('start')
            ->get();

        return collect([
            'timeouts' => $timeouts,
        ]);
    }

    public function notes()
    {
        $notes = Note::between($this->from, $this->to)
            ->get();

        return collect([
            'notes' => $notes,
        ]);
    }

    public function testsIP201611()
    {
        return $this->tests(IntroductionPerformance_201611::class);
    }

    private function tests($class)
    {
        $tests = Test::between($this->from, $this->to)
            ->whereType($class)
            ->get();

        $keys = [];

        foreach ((new $class)->getProperties() as $property) {
            $keys[$property] = ucwords(str_replace('_', ' ', Str::snake($property)));
        }

        return collect([
            'keys'  => $keys,
            'tests' => $tests,
        ]);
    }

    public function testsCP201611()
    {
        return $this->tests(ConcludingPerformance_201611::class);
    }

    public function talks()
    {
        $talks = Talk::between($this->from, $this->to, 'date')->get();

        return collect([
            'talks' => $talks,
        ]);
    }

    public function goals()
    {
        $goals = Goal::all();

        return collect([
            'goals' => $goals,
        ]);
    }

    public function stakeholders()
    {
        $stakeholders = Stakeholder::whereIn('id', $this->stakeholderIds)->get();

        return collect([
            'stakeholders' => $stakeholders,
        ]);
    }

    public function stakeholderQuestions()
    {
        $questionIds = Answer::groupBy('question_id')
            ->review()
            ->where('date', '>', $this->from)
            ->where('date', '<=', $this->to)
            ->get(['question_id'])
            ->pluck('question_id');

        $questions = Question::whereIn('id', $questionIds)
            ->with('history')
            ->get();

        return collect([
            'questions' => $questions,
        ]);
    }

    public function stakeholderQuestionRevisions()
    {
        $questionHistoryIds = Answer::groupBy('question_history_id')
            ->review()
            ->where('date', '>', $this->from)
            ->where('date', '<=', $this->to)
            ->get(['question_history_id'])
            ->pluck('question_history_id');

        $questionRevisions = QuestionHistory::whereIn('id', $questionHistoryIds)
            ->get();

        return collect([
            'revisions' => $questionRevisions,
        ]);
    }

    public function stakeholderAnswers()
    {
        $answers = Answer::review()
            ->where('date', '>', $this->from)
            ->where('date', '<=', $this->to)
            ->get();

        return collect([
            'answers' => $answers,
        ]);
    }

    public function reviews()
    {
        $reviews = Review::where('visible_at', '>', $this->from)
            ->where('visible_at', '<=', $this->to)
            ->get();

        return collect([
            'reviews' => $reviews,
        ]);
    }
}
