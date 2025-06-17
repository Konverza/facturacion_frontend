<?php
namespace App\Livewire\Business\Tables;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BusinessProduct;
use App\Exports\Business\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class Products extends Component
{
    use WithPagination;

    public $perPage = 10;
    public $search = ''; // Búsqueda general (puedes mantenerla o eliminarla)
    public $searchName = '';
    public $searchCode = '';
    public $exactSearch = false;
    public $stockState = 'todos';
    public $sortField = 'id';
    public $sortDirection = 'desc';

    public $options = [
        'todos' => 'Todos',
        'disponible' => 'Disponible',
        'por_agotarse' => 'Por agotarse',
        'agotado' => 'Agotado',
        'n/a' => 'Sin Inventario',
    ];

    // Resetear paginación cuando cambie cualquier parámetro de búsqueda
    public function updating($name, $value)
    {
        if (in_array($name, ['search', 'searchName', 'searchCode', 'exactSearch', 'stockState', 'perPage'])) {
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
        $this->reset(['search', 'searchName', 'searchCode', 'exactSearch', 'stockState']);
        $this->resetPage();
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
            ->when($this->searchName, function ($query) {
                $query->where('descripcion', $this->exactSearch ? $this->searchName : 'like', "%{$this->searchName}%");
            })
            ->when($this->searchCode, function ($query) {
                $query->where('codigo', $this->exactSearch ? $this->searchCode : 'like', "%{$this->searchCode}%");
            })
            ->when($this->stockState, function ($query) {
                if ($this->stockState !== 'todos' && $this->stockState !== 'n/a') {
                    $query->where('estado_stock', $this->stockState);
                } elseif ($this->stockState === 'n/a') {
                    $query->where('has_stock', false);
                }
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.business.tables.products', [
            'products' => $products,
        ]);
    }

    public function exportToExcel(): BinaryFileResponse
    {
        $fileName = 'productos_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new ProductsExport(
                $this->search,
                $this->searchName,
                $this->searchCode,
                $this->exactSearch,
                $this->stockState
            ),
            $fileName
        );
    }
}