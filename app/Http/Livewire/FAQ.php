<?php

namespace App\Http\Livewire;

use App\Http\Livewire\DataTable\WithBulkActions;
use App\Http\Livewire\DataTable\WithCachedRows;
use App\Http\Livewire\DataTable\WithPerPagePagination;
use App\Http\Livewire\DataTable\WithSorting;
use Livewire\Component;

class FAQ extends Component
{
    use WithPerPagePagination, WithBulkActions, WithCachedRows, WithSorting;

    public $filters = [
        'search'     => '',
        'status'     => '',
        'amount-min' => null,
        'amount-max' => null,
        'date-min'   => null,
        'date-max'   => null,
    ];


    protected $queryString = ['sorts'];

    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function getRowsQueryProperty()
    {
        $query = \App\Models\Faq::query()
            ->when(count($this->sorts) === 0, fn($query, $status) => $query->orderBy('question', 'asc'))
            ->when($this->filters['search'],
                fn($query, $search) => $query->search($search));

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
        return view('livewire.faq', [
            'faqs' => $this->rows,
        ]);
    }
}
