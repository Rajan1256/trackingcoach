<?php

namespace App\Listeners;

use App\Events\DailyScoresWereUpdated;
use App\Events\WeeklyScoresWereUpdated;
use App\Models\Answer;
use App\Models\Question;
use App\Models\WeeklyScore;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RegenerateWeeklyScores
{
    /**
     * Handle the event.
     *
     * @param  DailyScoresWereUpdated  $event
     *
     * @return void
     */
    public function handle(DailyScoresWereUpdated $event)
    {
        $start = (new Carbon($event->entry->date))->startOfWeek();
        $end = (new Carbon($event->entry->date))->endOfWeek();

        $week = $start->format('W');
        $year = $start->format('o');

        $answers = Answer::where('user_id', $event->entry->user_id)
            ->where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->get()->groupBy('question_id');

        $questions = Question::whereIn('id', $answers->keys())->with(['user'])->get();

        $questions->each(function (Question $question) use ($answers, $week, $year) {
            $score = $this->calculateScore($question, $answers->get($question->id));

            if ($score === false) {
                return;
            }

            WeeklyScore::updateOrCreate([
                'team_id'     => $question->team_id,
                'user_id'     => $question->user_id,
                'question_id' => $question->id,
                'week'        => $week,
                'year'        => $year,
            ], [
                'days_per_week'       => $question->user->days_per_week ?? 7, // @todo Jaco: FIX THIS
                'question_history_id' => $answers->get($question->id)->first()->question_history_id,
                'score'               => $score,
                'extra_data'          => $this->generateExtraData($answers->get($question->id)) ?? [],
            ]);
        });

        event(new WeeklyScoresWereUpdated($event->entry));
    }

    private function calculateScore(Question $question, $answers)
    {
        /** @var Answer $answer */
        $answer = $answers->first();
        $class = 'App\\Questions\\'.$answer->questionHistory->type;

        return (new $class)->calculateWeeklyScore($answers);
    }

    protected function generateExtraData(Collection $answers)
    {
        $class = 'App\\Questions\\'.$answers->first()->questionHistory->type;

        return (new $class)->generateWeeklyScoreData($answers);
    }
}
