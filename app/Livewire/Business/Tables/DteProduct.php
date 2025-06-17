<?php

namespace App\Livewire\Business\Tables;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BusinessProduct;

class DteProduct extends Component
{
    use WithPagination;

    public $perPage = 5;
    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';

    public $dte;
    public $number;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $products = BusinessProduct::where("business_id", session("business"))
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('codigo', 'like', "%{$this->search}%")
                        ->orWhere('descripcion', 'like', "%{$this->search}%");
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
        return view('livewire.business.tables.dte-product', [
            'products' => $products,
        ]);
    }
}
