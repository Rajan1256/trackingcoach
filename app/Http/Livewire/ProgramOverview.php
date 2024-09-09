<?php

namespace App\Http\Livewire;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class ProgramOverview extends Component
{
    use AuthorizesRequests;

    public User $customer;

    public $newMilestoneDate = null;

    public $newMilestoneTitle = null;

    protected $rules = [
        'newMilestoneDate'  => 'required',
        'newMilestoneTitle' => 'required',
    ];

    /**
     * @param  User  $customer
     */
    public function mount(User $customer)
    {
        $this->customer = $customer;
    }

    public function save()
    {
        $this->validate();

        $this->customer->programMilestones()->create([
            'team_id' => current_team()->id,
            'date'    => $this->newMilestoneDate,
            'title'   => $this->newMilestoneTitle,
        ]);

        $this->reset(['newMilestoneDate', 'newMilestoneTitle']);
        $this->customer->load('programMilestones');
    }

    public function deleteMilestone($id)
    {
        $milestone = $this->customer->programMilestones()->where('id', $id)->firstOrFail();
        $this->authorize('delete', $milestone);

        $milestone->delete();

        return redirect(request()->header('Referer'));
    }

    public function render()
    {
        return view('livewire.program-overview', [
            'milestones' => $this->customer->programMilestones,
        ]);
    }
}
