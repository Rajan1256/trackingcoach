<?php

namespace App\Http\Livewire;

use App\Enum\Roles;
use App\Http\Livewire\DataTable\WithBulkActions;
use App\Http\Livewire\DataTable\WithCachedRows;
use App\Http\Livewire\DataTable\WithPerPagePagination;
use App\Http\Livewire\DataTable\WithSorting;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Spatie\TranslationLoader\LanguageLine;

use function current_team;

class Translations extends Component
{
    use WithPerPagePagination, WithBulkActions, WithCachedRows, WithSorting, AuthorizesRequests;

    public $archived;

    public $filters = [
        'search'     => '',
        'status'     => '',
        'amount-min' => null,
        'amount-max' => null,
        'date-min'   => null,
        'date-max'   => null,
    ];


    protected $queryString = ['sorts'];

    public function getRowsProperty()
    {
        return $this->cache(function () {
            return $this->applyPagination($this->rowsQuery);
        });
    }

    public function getRowsQueryProperty()
    {
        $query = LanguageLine::orderBy('key')
            ->when($this->filters['search'],
                fn($query, $search) => $query->where('key', 'like', '%'.$search.'%')->orWhere('text', 'like', '%'.$search.'%'));

        return $this->applySorting($query);
    }

    public function mount()
    {
        abort_if(!current_team()->isRoot(), 403);
        abort_if(!Auth::user()->hasCurrentTeamRole([Roles::ADMIN]), 403);
    }

    public function render()
    {
        return view('livewire.translations', [
            'lines' => $this->rows,
        ]);
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }
}
