<?php

namespace App\Reports;

use App\Models\Answer;
use App\Models\QuestionHistory;
use App\Models\Review;
use App\Models\User;

class ReviewReport
{
    /**
     * @var User
     */
    public $customer;

    /**
     * @var Review
     */
    public $review;

    /**
     * ReviewReport constructor.
     *
     * @param  Review  $review
     * @param  User  $customer
     */
    public function __construct(Review $review, User $customer)
    {
        $this->review = $review;
        $this->customer = $customer;
        $this->answers = $this->getAnswersForReview();
        $this->questions = $this->getUniqueQuestions();
    }

    private function getAnswersForReview()
    {
        return $this->review->answers()
            ->with(['questionHistory', 'supporter'])
            ->join('questions', 'answers.question_id', '=', 'questions.id')
            ->orderBy('position')
            ->get()
            ->map(function ($model) {
                $className = '\\App\\Questions\\'.$model->question->type;
                $model->helper = (new $className($model->questionHistory));

                return $model;
            });
    }

    private function getUniqueQuestions()
    {
        return $this->answers
            ->unique('question_id')
            ->map(function (Answer $answer) {
                return $answer->questionHistory;
            });
    }

    public function getData()
    {
        return collect([
            'customer'  => $this->customer,
            'review'    => $this->review,
            'answers'   => $this->answers,
            'questions' => $this->questions,
            'raw'       => $this->getRawDataPerQuestion(),
        ]);
    }

    private function getRawDataPerQuestion()
    {
        $answers = $this->answers;

        return $this->questions->map(function (QuestionHistory $question) use ($answers) {
            $questionAnswers = $answers->where('question_id', $question->question_id)
                ->filter(function ($answer) {
                    return !empty($answer->supporter);
                });

            $className = '\\App\\Questions\\'.$question->question->type;
            $helper = (new $className($question));

            return collect([
                'question' => $question,
                'answers'  => $questionAnswers,
                'graph'    => $helper->fetchReviewGraph($questionAnswers, $this->review),
            ]);
        });
    }
}
