<?php

namespace App\Http\Controllers\App;

use App\Events\EntryWasSubmitted;
use App\Models\Answer;
use App\Models\Entry;
use App\Models\Membership;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DailyTracklistController
{
    public function show(Request $request, $token)
    {
        $membership = Membership::where('paired_app_token', $token)->first();
        $settings = $membership->user->getSettings($membership->team);
        $user = $membership->user;
        $team = $membership->team;
        $team->makeCurrent();

        $questionsCount = $user->questions()->tracklist()->count();

        $invite = $user->invites()->notExpired()->type('tracklist')->first();

        if (!$invite) {
            return [
                'token' => JWT::encode(
                    [
                        'total_questions_count' => $user->getTrackList()->count(),
                        'open_questions_count'  => 0,
                    ],
                    md5($token)
                ),
            ];
        }

        $date = (new \Carbon\Carbon($invite->options->get('date')));

        $questions = $user->getTrackList($date)->map(function ($question) use ($user) {
            return [
                'name'             => replaceNames($question->model->name, $user),
                'question_type'    => $question->viewFolder(),
                'question_id'      => $question->model->getLatestVersion()->id,
                'question_options' => $question->model->options->toArray(),
            ];
        })->values();

        $data = [
            'questions'             => $questions,
            'date'                  => $invite->options->get('date'),
            'isToday'               => $date->isToday(),
            'total_questions_count' => $questions->count(),
            'open_questions_count'  => count($questions),
        ];

        return [
            'token' => JWT::encode(
                $data,
                md5($token)
            ),
        ];
    }

    public function store(Request $request, $token)
    {
        $membership = Membership::where('paired_app_token', $token)->first();
        $settings = $membership->user->getSettings($membership->team);
        $user = $membership->user;
        $team = $membership->team;
        $team->makeCurrent();

        $invite = $user->invites()->notExpired()->type('tracklist')->first();

        $date = (new Carbon($invite->options->get('date')));

        Answer::store($request->answers, $user, $date);

        $entry = Entry::firstOrNew([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'date'    => $date,
        ])->fill([
            'invite_id' => $invite->id,
            'author_id' => $user->id,
            'scope'     => 'tracklist',
        ]);

        $entry->save();

        event(new EntryWasSubmitted($entry));

        $invite->update([
            'expires_at' => now(),
        ]);

        return [
            'success' => true,
        ];
    }
}
