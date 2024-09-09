<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Supporter;
use App\Models\User;
use Illuminate\Http\Request;

class ReviewSupporterResultController extends Controller
{
    public function __invoke(Request $request, User $customer, Review $review, Supporter $supporter)
    {
        $answers = $review->answers()
            ->where('supporter_id', $supporter->id)
            ->with('questionHistory')
            ->get()
            ->map(function ($model) {
                $className = '\\App\\Questions\\'.$model->question->type;
                $model->helper = (new $className($model->questionHistory));

                return $model;
            });

        return view('customers.reviews.showResults', [
            'customer'  => $customer,
            'answers'   => $answers,
            'review'    => $review,
            'supporter' => $supporter,
        ]);
    }
}
