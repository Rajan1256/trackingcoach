<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CopyTemplateReviewQuestionsController extends Controller
{
    public function __invoke(Request $request, User $customer, Question $question)
    {
        $newQuestion = Question::create(array_merge($question->toArray(), [
            'scope'     => 'review',
            'parent_id' => $question->id,
            'user_id'   => $customer->id,
            'position'  => ($customer->questions->sortByDesc('position')->first()?->position ?? 0) + 1,
        ]));

        QuestionHistory::create(array_merge($question->getLatestVersion()->toArray(), [
            'question_id' => $newQuestion->id,
            'starts_at'   => Carbon::createFromTimestamp($question->getLatestVersion()->starts_at),
        ]));

        session()->flash('edit_question', $newQuestion->id);

        return redirect()->back();
    }
}
