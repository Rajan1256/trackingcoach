<?php

namespace App\Http\Controllers\App;

use App\Models\DailyScore;
use App\Models\Membership;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;

class HistoryController
{
    public function __invoke(Request $request, $token)
    {
        $membership = Membership::where('paired_app_token', $token)->first();
        $user = $membership->user;
        $team = $membership->team;
        $team->makeCurrent();

        $verbatimIds = $user->questions()->tracklist()->get()->filter(function ($question) {
            return $question->type == 'Verbatim';
        })->pluck('id');

        $verbatims = $user->answers()->whereIn('question_id',
            $verbatimIds)->orderBy('date', 'desc')->distinct()->paginate(50, ['date', 'user_id']);

        $verbatims->map(function ($model) use ($verbatimIds) {
            $model->texts = $model->user->answers()->whereIn('question_id', $verbatimIds)->where('date',
                $model->date)->get(['answer_text'])->pluck('answer_text')->toArray();
            $model->answer_text = $model->texts[0] ?? '';
            $model->score = round(DailyScore::where('date', $model->date)
                ->where('user_id', $model->user_id)
                ->avg('score'));
            return $model;
        });


        return [
            'token' => JWT::encode(
                ['verbatims' => $verbatims],
                md5($token)
            ),
        ];
    }
}
