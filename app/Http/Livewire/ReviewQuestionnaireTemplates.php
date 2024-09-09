<?php

namespace App\Http\Livewire;

use App\Models\Question;
use App\Models\User;
use App\Questions\NumericDaily;
use App\Questions\NumericWeekly;
use App\Questions\SevenScale;
use App\Questions\Verbatim;
use App\Questions\YesNo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

use function strlen;

class ReviewQuestionnaireTemplates extends Component
{
    public User $customer;

    public Collection $templates;

    public string $newQuestion = '';

    public string $searchValue = '';

    public string $newType = '';

    public Collection $questionTypes;

    public ?int $editingQuestion;

    public function mount(User $customer)
    {
        $this->editingQuestion = session()->get('edit_question', null);
        $this->customer = $customer;
        $this->templates = collect();

        $this->newQuestion = old('name', '');
        $this->newType = old('type', '');

        $this->updateTemplates();
    }

    public function updateTemplates()
    {
        $templates = Question::where('scope', 'reviewTemplate');

        if (strlen($this->searchValue) > 0) {
            $templates->whereHas('histories', function (Builder $builder) {
                $builder->where(DB::raw('LOWER(`name`)'), 'LIKE', '%'.Str::lower($this->searchValue).'%');
            });
        }

        $this->templates = $templates->orderBy('position')->get();
    }

    public function editQuestion($id)
    {
        $this->editingQuestion = $id;
    }

    public function updateQuestionOrder($order)
    {
        $newOrder = collect($order)->keyBy('value')->map(fn($value) => $value['order']);
        Question::where('scope', 'reviewTemplate')->sorted()->get()->each(function (Question $question) use (
            $newOrder
        ) {
            if ($position = $newOrder->get($question->id, null)) {
                $question->update(['position' => $position]);
            }
        });

        $this->updateTemplates();
    }

    public function render()
    {
        $this->questionTypes = collect([
            new SevenScale,
            new YesNo,
            new NumericDaily,
            new NumericWeekly,
            new Verbatim,
        ])->keyBy(fn($questionType) => get_class($questionType));

        return view('livewire.review-questionnaire-templates')
            ->with([
                'customer'  => $this->customer,
                'questions' => $this->templates->map(function (
                    Question $model
                ) {
                    return $model->present();
                }),
            ]);
    }
}
