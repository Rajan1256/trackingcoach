<?php

namespace App\Http\Controllers;

use App\Events\EntryWasSubmitted;
use App\Models\Answer;
use App\Models\Entry;
use App\Models\Invite;
use App\Models\Question;
use App\Models\Review;
use App\Models\Supporter;
use App\Reports\MonthReport;
use App\Reports\WeekReport;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CodeController extends Controller
{
    public function show(Request $request, string $code)
    {
        $invite = Invite::where('code', $code)->notExpired()->first();

        if (!$invite) {
            return view('code-expired');
        }

        if ($invite->type == 'supporter_review_invite') {
            return $this->presentSupporterReview($invite);
        }

        // for other requests
        $this->authenticateCustomer($invite);

        switch ($invite->type) {
            case 'tracklist':
                app()->setLocale($invite->user->locale ?? config('app.locale'));

                $date = (new Carbon($invite->options->get('date')));

                $questions = $invite->user->getTrackList($date);

                return view('questions.tracklist', [
                    'invite'    => $invite,
                    'questions' => $questions,
                ]);
            case 'weeklyreport':
                return $this->presentWeeklyReport($invite);
            case 'monthlyreport':
                return $this->presentMonthlyReport($invite);
        }
    }

    private function presentSupporterReview(Invite $invite)
    {
        $supporter = Supporter::findOrFail($invite->options->get('supporter'));
        $review = Review::findOrFail($invite->options->get('review'));

        abort_if($review->answers->where('supporter_id', $supporter->id)->count() > 0, 404);

        app()->setLocale($supporter->locale);

        $customer = $invite->user;

        $coach = $customer->coach($invite->team_id)->first() ?? $invite->team->owner;

        $questions = $customer->questions()->review()->get()->map(function (Question $model) {
            return $model->present();
        });

        return view('reviews.form', [
            'questions' => $questions,
            'supporter' => $supporter,
            'customer'  => $customer,
            'coach'     => $coach,
        ]);
    }

    private function authenticateCustomer(Invite $invite)
    {
        if (!auth()->check() || auth()->id() != $invite->user_id) {
            auth()->onceUsingId($invite->user_id);
        }
    }

    private function presentWeeklyReport(Invite $invite)
    {
        $data = (new WeekReport($invite->user, $invite->options->get('year'),
            $invite->options->get('week')))->getData();

        return view('scores.weekly', ['data' => $data, 'minimal' => true]);
    }

    private function presentMonthlyReport(Invite $invite)
    {
        $data = (new MonthReport($invite->user, $invite->options->get('year'),
            $invite->options->get('month')))->getData();

        return view('scores.monthly', ['data' => $data, 'minimal' => true]);
    }

    public function store(Request $request, $code)
    {
        $invite = Invite::where('code', $code)->notExpired()->firstOrFail();

        if ($invite->type == 'supporter_review_invite') {
            return $this->saveReview($request, $invite);
        }

        $this->authenticateCustomer($invite);

        if ($invite->type == 'tracklist') {
            $date = (new Carbon($invite->options->get('date')));

            Answer::store($request->input('answers', []), $invite->user, $date);
            $entry = Entry::firstOrNew([
                'scope'   => 'tracklist',
                'team_id' => $invite->team_id,
                'user_id' => $invite->user_id,
                'date'    => $date,
            ])->fill([
                'invite_id' => $invite->id,
                'author_id' => $invite->user_id,
            ]);
            $entry->save();

            $invite->expires_at = Carbon::now();
            $invite->save();

            event(new EntryWasSubmitted($entry));

            return view('questions.thankyou');
        }
    }

    private function saveReview(Request $request, Invite $invite)
    {
        $date = (new Carbon($invite->created_at));
        $supporter = Supporter::findOrFail($invite->options->get('supporter'));
        $review = Review::findOrFail($invite->options->get('review'));

        Answer::storeReview($request->input('answers', []), $supporter, $review, $date);

        $entry = Entry::firstOrNew([
            'team_id' => $invite->team_id,
            'user_id' => $invite->user_id,
            'date'    => $date,
            'scope'   => 'review',
        ])->fill([
            'invite_id' => $invite->id,
        ]);
        $entry->save();

        return view('reviews.thankyou', [
            'customer' => $invite->user,
        ]);
    }
}
