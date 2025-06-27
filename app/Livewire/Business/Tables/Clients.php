<?php

namespace App\Livewire\Business\Tables;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BusinessCustomer;
use App\Exports\Business\CustomerExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Services\OctopusService;

class Clients extends Component
{
    use WithPagination;
    
    protected $octopusService;
    public $perPage = 10;
    public $search = '';
    public $searchNombre = '';
    public $searchNumDocumento = '';
    public $exactSearch = false;
    public $departamentos = [];
    public $sortField = 'id';
    public $sortDirection = 'desc';
    public $document_types = [
        "02" => "Carnet de Residente",
        "03" => "Pasaporte",
        "13" => "DUI",
        "36" => "NIT",
        "37" => "Otro",
    ];

    public function __construct()
    {
        $this->octopusService = new OctopusService();
        $this->departamentos = $this->octopusService->simpleDepartamentos();
    }

    public function updating($name, $value)
    {
        if (in_array($name, ['search', 'searchNombre', 'searchNumDocumento', 'exactSearch', 'departamentos', 'municipios'])) {
            $this->resetPage();
        }
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

    public function clearFilters()
    {
        $this->reset(['search', 'searchNombre', 'searchNumDocumento', 'exactSearch', 'departamentos', 'municipios']);
        $this->resetPage();
    }

    public function render()
    {
        $customers = BusinessCustomer::where("business_id", session("business"))
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('numDocumento', 'like', "%{$this->search}%")
                        ->orWhere('nrc', 'like', "%{$this->search}%")
                        ->orWhere('nombre', 'like', "%{$this->search}%")
                        ->orWhere('nombreComercial', 'like', "%{$this->search}%");
                });
            })
            ->when($this->searchNombre, function ($query) {
                $query->where('nombre', $this->exactSearch ? $this->searchNombre : 'like', "%{$this->searchNombre}%")
                    ->orWhere('nombreComercial', $this->exactSearch ? $this->searchNombre : 'like', "%{$this->searchNombre}%");
            })
            ->when($this->searchNumDocumento, function ($query) {
                $query->where('numDocumento', $this->exactSearch ? $this->searchNumDocumento : 'like', "%{$this->searchNumDocumento}%")
                    ->orWhere('nrc', $this->exactSearch ? $this->searchNumDocumento : 'like', "%{$this->searchNumDocumento}%");
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.business.tables.clients', [
            'customers' => $customers,
            'departamentos' => $this->departamentos,
        ]);
    }

    public function exportToExcel(): BinaryFileResponse
    {
        $fileName = 'clientes_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new CustomerExport(
                $this->search,
                $this->searchNombre,
                $this->searchNumDocumento,
                $this->exactSearch,
                $this->departamentos,
                $this->octopusService
            ),
            $fileName
        );
    }
}
