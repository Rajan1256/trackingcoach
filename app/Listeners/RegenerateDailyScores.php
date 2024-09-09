<?php

namespace App\Listeners;

use App\Events\DailyScoresWereUpdated;
use App\Events\EntryWasSubmitted;
use App\Models\Answer;
use App\Models\DailyScore;

class RegenerateDailyScores
{
    /**
     * Handle the event.
     *
     * @param  EntryWasSubmitted  $event
     *
     * @return void
     */
    public function handle(EntryWasSubmitted $event)
    {
        $event->entry->answers()->with('question')->each(function (Answer $answer) {
            $score = $this->calculateScore($answer);

            if ($score === false) {
                return;
            }

            DailyScore::firstOrNew([
                'team_id'     => $answer->question->team_id,
                'user_id'     => $answer->user_id,
                'question_id' => $answer->question_id,
                'date'        => $answer->date,
            ])->fill([
                'question_history_id' => $answer->question_history_id,
                'score'               => $score,
                'extra_data'          => $this->generateExtraData($answer),
            ])->save();
        });

        event(new DailyScoresWereUpdated($event->entry));
    }

    private function calculateScore(Answer $answer)
    {
        $class = 'App\\Questions\\'.$answer->questionHistory->type;

        return (new $class)->calculateDailyScore($answer);
    }

    private function generateExtraData(Answer $answer)
    {
        $class = 'App\\Questions\\'.$answer->questionHistory->type;

        return (new $class)->getDailyGraphData($answer);
    }
}
