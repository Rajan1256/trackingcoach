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

use function old;
use function session;
use function strlen;

class Questionnaire extends Component
{
    public User $customer;

    public Collection $templates;

    public string $newQuestion = '';

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
    }

    public function editQuestion($id)
    {
        $this->editingQuestion = $id;
    }

    public function updateQuestionOrder($order)
    {
        $newOrder = collect($order)->keyBy('value')->map(fn($value) => $value['order']);
        $this->customer->questions()->tracklist()->sorted()->get()->each(function (Question $question) use (
            $newOrder
        ) {
            if ($position = $newOrder->get($question->id, null)) {
                $question->update(['position' => $position]);
            }
        });
    }

    public function updateTemplates()
    {
        $templates = collect();

        if (strlen($this->newQuestion) > 0) {
            $templates = Question::where('scope', 'tracklistTemplate')
                ->whereHas('histories', function (Builder $builder) {
                    $builder->where(DB::raw('LOWER(`name`)'), 'LIKE', '%'.Str::lower($this->newQuestion).'%');
                })
                ->get();
        }

        $this->templates = $templates;
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

        return view('livewire.questionnaire')
            ->with([
                'customer'  => $this->customer,
                'questions' => $this->customer->questions()->tracklist()->sorted()->get()->map(function (Question $model
                ) {
                    return $model->present();
                }),
            ]);
    }
}
