<?php

namespace App\Questions;

use App\Http\Requests\Questions\StoreQuestionRequest;
use App\Http\Requests\Questions\UpdateQuestionRequest;
use App\Interfaces\QuestionInterface;
use App\Models\Answer;
use App\Models\Question;
use App\Models\QuestionHistory;
use App\Models\Review;
use App\Models\Supporter;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;

use function is_null;

abstract class AbstractQuestion implements QuestionInterface
{

    /**
     * @var Question|null
     */
    public ?Question $model = null;

    /**
     * @var QuestionHistory|null
     */
    protected ?QuestionHistory $questionHistory = null;

    /**
     * @var string
     */
    protected string $savesTo = 'answer_text';

    /**
     * @var bool
     */
    protected bool $translatable = false;

    /**
     * @param  Question|QuestionHistory|null  $model
     */
    public function __construct(Question|QuestionHistory $model = null)
    {
        if (!is_null($model)) {
            if ($model instanceof Question) {
                $this->model = $model;
            } elseif ($model instanceof QuestionHistory) {
                $this->questionHistory = $model;
            }
        }
    }

    public abstract function getIcon(): ?string;

    /**
     * @return bool
     */
    final public function isTranslatable()
    {
        return (bool) $this->translatable;
    }

    /**
     * @return string
     */
    public static function dbColumn(): string
    {
        return (new static())->savesTo;
    }

    /**
     * @return array
     */
    public static function updateRules(): array
    {
        return static::storeRules();
    }

    /**
     * @return array
     */
    public static function storeRules(): array
    {
        return [];
    }

    /**
     * @param  StoreQuestionRequest  $request
     * @param  User|null  $user
     *
     * @return Question
     */
    public function create(StoreQuestionRequest $request, User $user = null): Question
    {
        $oldLocale = app()->getLocale();
        app()->setLocale('en');

        $data = [
            'name'        => is_string($request->get('name')) ? [
                Auth::user()->locale => $request->get('name', ''),
            ] : $request->get('name', ''),
            'description' => is_string($request->get('description')) ? [
                Auth::user()->locale => $request->get('description', ''),
            ] : $request->get('description', ''),
            'type'        => class_basename($request->get('type')),
            'options'     => $this->formatOptions($request->get('options', [])),
            'starts_at'   => Carbon::now(),
        ];

        if ($user) {
            $question = $user->questions()->create([
                'team_id'  => current_team()->id,
                'scope'    => $request->get('scope', 'tracklist'),
                'position' => $user->questions()->whereScope($request->get('scope', 'tracklist'))->count() + 1,
            ]);

            $question->histories()->create($data);
//            $question = $user->questions()->create($data);
        } else {
            $question = Question::create($data);
        }

        app()->setLocale($oldLocale);

        return $question;
    }

    /**
     * @param  array  $options
     *
     * @return array
     */
    public function formatOptions($options = []): array
    {
        return $options;
    }

    /**
     * @param  Answer  $answer
     *
     * @return mixed
     */
    public function displayAnswer(Answer $answer)
    {
        return $answer->{$this->savesTo};
    }

    /**
     * @param $answers
     * @param  Review  $review
     *
     * @return mixed
     */
    public function fetchReviewGraph($answers, Review $review)
    {
    }

    /**
     * @param $answers
     * @param  Review  $review
     *
     * @return mixed
     */
    public function generateReviewGraph($answers, Review $review)
    {
    }

    /**
     * @return string|null
     */
    public function getArchiveUrl(): string|null
    {
        return match ($this->model?->scope) {
            'tracklist' => route('users.tracklist.destroy',
                ['user' => $this->model->user_id, 'question' => $this->model->id]),
            'review' => route('users.reviewQuestionnaire.destroy',
                ['user' => $this->model->user_id, 'question' => $this->model->id]),
            'reviewTemplate' => route('reviewQuestions.destroy', ['question' => $this->model->id]),
            'tracklistTemplate' => route('tracklistQuestions.destroy', ['question' => $this->model->id]),
            default => null,
        };
    }

    /**
     * @param  Answer  $answer
     *
     * @return string
     */
    public function getDayTargetString(Answer $answer)
    {
        return '[day target here]';
    }

    /**
     * @return string|null
     */
    public function getEditUrl(): string|null
    {
        return match ($this->model?->scope) {
            'tracklist' => route('users.tracklist.edit',
                ['user' => $this->model->user_id, 'question' => $this->model->id]),
            'review' => route('users.reviewQuestionnaire.edit',
                ['user' => $this->model->user_id, 'question' => $this->model->id]),
            'reviewTemplate' => route('reviewQuestions.edit', ['question' => $this->model->id]),
            'tracklistTemplate' => route('tracklistQuestions.edit', ['question' => $this->model->id]),
            default => null,
        };
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return trans('trackingcoach.questions.types.'.$this->getIdentifier());
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return substr(strrchr(static::class, '\\'), 1);
    }

    /**
     * @return bool
     */
    public function showOnlyWeekdaysLabel(): bool
    {
        return $this->model?->scope == 'tracklist' &&
            $this->model?->options->get('excludeWeekends', 0) == 1 &&
            $this->model?->user->days_per_week == 7;
    }

    /**
     * @param  User  $user
     * @param $answer
     * @param  QuestionHistory  $history
     * @param  Carbon  $date
     *
     * @return bool|null
     */
    public function storeAnswer(User $user, $answer, QuestionHistory $history, Carbon $date): bool|null
    {
        if (!$this->shouldContinue($answer)) {
            return null;
        }

        return Answer::firstOrNew([
            'team_id'             => current_team()->id,
            'user_id'             => $user->id,
            'question_id'         => $history->question->id,
            'question_history_id' => $history->id,
            'scope'               => 'tracklist',
            'date'                => $date,
        ])->fill([
            $this->savesTo => $this->prepareAnswerForDb($answer),
        ])->save();
    }

    /**
     * @param $answer
     *
     * @return bool
     */
    protected function shouldContinue($answer)
    {
        return true;
    }

    /**
     * @param $answer
     *
     * @return mixed
     */
    public function prepareAnswerForDb($answer)
    {
        return $answer;
    }

    /**
     * @param  Supporter  $supporter
     * @param  Review  $review
     * @param $answer
     * @param  QuestionHistory  $history
     * @param  Carbon  $date
     *
     * @return bool|null
     */
    public function storeReviewAnswer(
        Supporter $supporter,
        Review $review,
        $answer,
        QuestionHistory $history,
        Carbon $date
    ): bool|null {
        if (!$this->shouldContinue($answer)) {
            return null;
        }

        return Answer::firstOrNew([
            'team_id'             => current_team()->id,
            'user_id'             => $supporter->user->id,
            'supporter_id'        => $supporter->id,
            'review_id'           => $review->id,
            'question_id'         => $history->question->id,
            'question_history_id' => $history->id,
            'scope'               => 'review',
        ])->fill([
            $this->savesTo => $this->prepareAnswerForDb($answer),
            'date'         => $date,
        ])->save();
    }

    /**
     * @param  UpdateQuestionRequest  $request
     * @param  Question  $question
     *
     * @return Question
     */
    public function update(UpdateQuestionRequest $request, Question $question): Question
    {
        $oldLocale = app()->getLocale();
        app()->setLocale('en');

        $question->histories()->create([
            'name'        => is_string($request->get('name')) ? ['en' => $request->get('name')] : $request->get('name'),
            'description' => is_string($request->get('description')) ? ['en' => $request->get('description')] : $request->get('description'),
            'type'        => class_basename($request->get('type')),
            'options'     => $this->formatOptions($request->get('options', [])),
            'starts_at'   => Carbon::now(),
            'author_id'   => auth()->user()->id,
        ]);

        app()->setLocale($oldLocale);

        return $question->fresh();
    }

    /**
     * @return string
     */
    public function viewFolder(): string
    {
        return Str::lower($this->getIdentifier());
    }

    /**
     * @param $score
     *
     * @return int
     */
    protected function normalizeScore($score)
    {
        if ($score < 0) {
            return 0;
        }
        if ($score > 100) {
            return 100;
        }

        return $score;
    }
}
