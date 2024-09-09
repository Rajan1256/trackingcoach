<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Questions\StoreQuestionRequest;
use App\Http\Requests\Questions\UpdateQuestionRequest;
use App\Models\Question;
use App\Models\User;
use Illuminate\Http\Request;

use function dd;

class ReviewQuestionsController extends Controller
{
    public function destroy(Request $request, User $customer, Question $question)
    {
        $customer->questions()->find($question->id)->delete();

        return redirect()->back();
    }

    public function store(StoreQuestionRequest $request, User $customer)
    {
        $request->merge(['scope' => 'review']);
        $class = $request->get('type');
        (new $class)->create($request, $customer);

        if ($request->get('template')) {
            $request->merge([
                'scope'   => 'reviewTemplate',
                'user_id' => null,
            ]);
            (new $class)->create($request, $customer);
        }

        return redirect()->back();
    }

    public function update(UpdateQuestionRequest $request, User $customer, Question $question)
    {
        $question->present()->update($request, $question);
        return redirect()->back();
    }
}
