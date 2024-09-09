<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reviews\StoreReviewRequest;
use App\Http\Requests\Reviews\UpdateReviewRequest;
use App\Models\Question;
use App\Models\Review;
use App\Models\User;
use App\Reports\ReviewReport;
use Illuminate\Http\Request;

class ReviewsController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Review::class, 'review');
    }

    public function create(Request $request, User $customer)
    {
        return view('customers.reviews.create', [
            'customer' => $customer,
        ]);
    }

    public function destroy(Request $request, User $customer, Review $review)
    {
        $review->delete();

        session()->flash('message', __('Successfully deleted review'));

        return redirect()->to(route('customers.reviews.index', [$customer]));
    }

    public function edit(Request $request, User $customer, Review $review)
    {
        return view('customers.reviews.edit', [
            'customer' => $customer,
            'review'   => $review,
        ]);
    }

    public function index(Request $request, User $customer)
    {
        $reviews = $customer->reviews;

        $questions = $customer->questions()->review()->get()->map(function (Question $model) {
            return $model->present();
        });

        return view('customers.reviews.index', [
            'customer'  => $customer,
            'reviews'   => $reviews,
            'questions' => $questions,
        ]);
    }

    public function show(Request $request, User $customer, Review $review)
    {
        $review->load(['answers', 'reviewInvitations']);

        $report = (new ReviewReport($review, $customer))->getData();

        $answers = $review->answers()
            ->with('questionHistory', 'question')
            ->join('questions', 'answers.question_id', '=', 'questions.id')
            ->orderBy('position')
            ->get()
            ->map(function ($model) {
                $className = '\\App\\Questions\\'.$model->question->type;
                $model->helper = (new $className($model->questionHistory));

                return $model;
            });

        return view('customers.reviews.show', [
            'customer' => $customer,
            'review'   => $review,
            'report'   => $report,
            'answers'  => $answers,
        ]);
    }

    public function store(StoreReviewRequest $request, User $customer)
    {
        $customer->reviews()->create([
            'team_id'    => current_team()->id,
            'name'       => $request->get('name'),
            'opens_at'   => $request->get('opens_at'),
            'closes_at'  => $request->get('closes_at'),
            'visible_at' => $request->get('visible_at'),
        ]);

        session()->flash('message', __('Successfully created review'));

        return redirect()->to(route('customers.reviews.index', [$customer]));
    }

    public function update(UpdateReviewRequest $request, User $customer, Review $review)
    {
        $review->update([
            'name'       => $request->get('name'),
            'opens_at'   => $request->get('opens_at'),
            'closes_at'  => $request->get('closes_at'),
            'visible_at' => $request->get('visible_at'),
        ]);

        session()->flash('message', __('Successfully updated review'));

        return redirect()->to(route('customers.reviews.index', [$customer]));
    }
}
