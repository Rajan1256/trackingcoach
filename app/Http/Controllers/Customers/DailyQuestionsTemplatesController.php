<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Questions\StoreQuestionRequest;
use App\Http\Requests\Questions\UpdateQuestionRequest;
use App\Models\Question;
use App\Models\User;
use Illuminate\Http\Request;

class DailyQuestionsTemplatesController extends Controller
{
    public function store(StoreQuestionRequest $request, User $customer)
    {
        $request->merge([
            'scope'   => 'tracklistTemplate',
            'user_id' => null,
        ]);
        $class = $request->get('type');
        (new $class)->create($request, $customer);

        return redirect()->back();
    }

    public function update(UpdateQuestionRequest $request, User $customer, Question $question)
    {
        $question->present()->update($request, $question);
        return redirect()->back();
    }

    public function destroy(Request $request, User $customer, Question $question)
    {
        $question->delete();

        return redirect()->back();
    }
}
