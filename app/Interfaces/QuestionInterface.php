<?php

namespace App\Interfaces;

use App\Http\Requests\Questions\StoreQuestionRequest;
use App\Models\Answer;
use App\Models\Question;
use App\Models\User;
use Illuminate\Support\Collection;

interface QuestionInterface
{
    /**
     * Return an array of rules when storing a question of this type.
     *
     * @return array
     */
    public static function storeRules(): array;

    /**
     * Calculate the daily score based on the given answer.
     * If the question type doesn't generate daily scores,
     * return false. Otherwise, return an integer or float.
     *
     * @param  Answer  $answer
     * @return float|int|false
     */
    public function calculateDailyScore(Answer $answer): float|int|false;

    /**
     * Calculate the weekly score based on the given answers.
     * Returns either an integer or a float.
     *
     * @param  Collection  $answers
     * @return float|int|false
     */
    public function calculateWeeklyScore(Collection $answers): float|int|false;

    /**
     * @param  StoreQuestionRequest  $request
     * @param  User  $client
     * @return Question
     */
    public function create(StoreQuestionRequest $request, User $client): Question;

    /**
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * @return string
     */
    public function getName(): string;
}
