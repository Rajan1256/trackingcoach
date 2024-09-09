<?php

namespace App\Http\Livewire;

use App\Enum\Roles;
use App\Http\Livewire\DataTable\WithBulkActions;
use App\Http\Livewire\DataTable\WithCachedRows;
use App\Http\Livewire\DataTable\WithPerPagePagination;
use App\Http\Livewire\DataTable\WithSorting;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

use function current_team;

class Customers extends Component
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

    public function mount()
    {
        $this->authorize('viewAny', User::class);
        $this->archived = request()->routeIs('customers.archived');
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function getRowsQueryProperty()
    {
        $query = current_team()
            ->users()
            ->when(count($this->sorts) === 0, fn($query, $status) => $query->orderBy('first_name', 'asc'))
//            ->when($this->filters['amount-min'], fn($query, $amount) => $query->where('amount', '>=', $amount))
//            ->when($this->filters['amount-max'], fn($query, $amount) => $query->where('amount', '<=', $amount))
//            ->when($this->filters['date-min'], fn($query, $date) => $query->where('date', '>=', Carbon::parse($date)))
//            ->when($this->filters['date-max'], fn($query, $date) => $query->where('date', '<=', Carbon::parse($date)))
            ->when($this->filters['search'],
                fn($query, $search) => $query->search($search));

        if ($this->archived) {
            $query->isArchivedCustomer(true);
        } else {
            $query->isCustomer(true);
        }
        if (!Auth::user()->hasCurrentTeamRole([Roles::ADMIN])) {
            if (Auth::user()->hasCurrentTeamRole([Roles::COACH])) {
                $query->partOfCoach();
            } elseif (Auth::user()->hasCurrentTeamRole([Roles::PHYSIOLOGIST])) {
                $query->partOfPhysiologist();
            } else {
                $query->where('users.id', Auth::id());
            }
        }

        return $this->applySorting($query);
    }

    public function getRowsProperty()
    {
        return $this->cache(function () {
            return $this->applyPagination($this->rowsQuery);
        });
    }

    public function render()
    {
        $showGrid = User::query()->partOfCurrentTeam()->count() <= 5;
        return view('livewire.customers', [
            'customers' => $this->rows,
            'archived'  => $this->archived,
            'showGrid'  => $showGrid,
        ]);
    }
}
