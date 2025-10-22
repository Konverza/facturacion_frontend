<?php
namespace App\Livewire\Business\Tables;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BusinessProduct;
use App\Models\BusinessUser;
use App\Models\Sucursal;
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

    // Propiedades para selección de sucursal
    public $selectedSucursalId = null;
    public $availableSucursales = [];
    public $canSelectBranch = false;
    public $defaultSucursalId = null;
    public $viewAllBranches = false; // Nueva propiedad para vista global

    public $options = [
        'todos' => 'Todos',
        'disponible' => 'Disponible',
        'por_agotarse' => 'Por agotarse',
        'agotado' => 'Agotado',
        'n/a' => 'Sin Inventario',
    ];

    public function mount()
    {
        // Obtener configuración del usuario
        $businessUser = BusinessUser::where('business_id', session('business'))
            ->where('user_id', auth()->id())
            ->first();

        if ($businessUser) {
            $this->canSelectBranch = (bool) $businessUser->branch_selector;

            // Obtener sucursal por defecto desde el POS
            if ($businessUser->default_pos_id) {
                $pos = $businessUser->defaultPos;
                if ($pos && $pos->sucursal_id) {
                    $this->defaultSucursalId = $pos->sucursal_id;
                    $this->selectedSucursalId = $this->defaultSucursalId;
                    
                    // Cargar nombre de la sucursal por defecto
                    if (!$this->canSelectBranch) {
                        $sucursal = Sucursal::find($this->defaultSucursalId);
                        if ($sucursal) {
                            $this->availableSucursales = [$sucursal->id => $sucursal->nombre];
                        }
                    }
                }
            }

            // Si puede seleccionar sucursales, cargar todas las disponibles
            if ($this->canSelectBranch) {
                $this->availableSucursales = Sucursal::where('business_id', session('business'))
                    ->orderBy('nombre')
                    ->get()
                    ->pluck('nombre', 'id')
                    ->toArray();
            }

            // Si no tiene POS por defecto, seleccionar la primera sucursal
            if (!$this->selectedSucursalId && !empty($this->availableSucursales)) {
                $this->selectedSucursalId = array_key_first($this->availableSucursales);
            }
        }
    }

    // Resetear paginación cuando cambie cualquier parámetro de búsqueda
    public function updating($name, $value)
    {
        if (in_array($name, ['search', 'searchName', 'searchCode', 'exactSearch', 'stockState', 'perPage', 'selectedSucursalId'])) {
            $this->resetPage();
        }
        
        // Si cambia la sucursal, detectar si es "ver todas"
        if ($name === 'selectedSucursalId') {
            $this->viewAllBranches = ($value === 'all');
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
        $query = BusinessProduct::where("business_id", session("business"));

        // Filtrar por sucursal seleccionada si aplica (incluyendo agotados)
        // No filtrar si está en modo "ver todas"
        if ($this->selectedSucursalId && !$this->viewAllBranches) {
            $selectedSucursalId = $this->selectedSucursalId; // evitar usar $this dentro del closure anidado
            $query->where(function ($q) use ($selectedSucursalId) {
                // Productos globales (siempre disponibles)
                $q->where('is_global', true)
                  // O productos con stock en la sucursal (incluyendo agotados)
                  ->orWhereHas('branchStocks', function ($stockQuery) use ($selectedSucursalId) {
                      $stockQuery->where('sucursal_id', $selectedSucursalId);
                  })
                  // O productos sin control de stock: disponibles si son globales o si tienen mapeo a la sucursal
                  ->orWhere(function ($q2) use ($selectedSucursalId) {
                      $q2->where('has_stock', false)
                         ->where(function ($q3) use ($selectedSucursalId) {
                             $q3->where('is_global', true)
                                ->orWhereHas('branchStocks', function ($stockQuery) use ($selectedSucursalId) {
                                    $stockQuery->where('sucursal_id', $selectedSucursalId);
                                });
                         });
                  });
            });
        }

        // Aplicar filtros de búsqueda
        $query->when($this->search, function ($query) {
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
            });

        // Filtrar por estado de stock (solo si hay sucursal seleccionada y no está en modo "ver todas")
        if ($this->selectedSucursalId && !$this->viewAllBranches && $this->stockState && $this->stockState !== 'todos') {
            if ($this->stockState === 'n/a') {
                $query->where(function ($q) {
                    $q->where('has_stock', false)
                      ->orWhere('is_global', true);
                });
            } else {
                $query->whereHas('branchStocks', function ($stockQuery) {
                    $stockQuery->where('sucursal_id', $this->selectedSucursalId)
                               ->where('estado_stock', $this->stockState);
                });
            }
        }

        $products = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        // Agregar información de stock por sucursal a cada producto
        $products->getCollection()->transform(function ($product) {
            if ($this->viewAllBranches) {
                // Modo "ver todas": mostrar inventario global por sucursal
                $product->stockPorSucursales = $product->branchStocks()
                    ->with('sucursal:id,nombre')
                    ->get()
                    ->map(function ($stock) {
                        return [
                            'sucursal_id' => $stock->sucursal_id,
                            'sucursal_nombre' => $stock->sucursal->nombre ?? 'Sin nombre',
                            'stock' => $stock->stockActual,
                            'estado' => $stock->estado_stock,
                        ];
                    });
                $product->stockPorSucursal = null;
                $product->estadoStockSucursal = null;
            } elseif ($this->selectedSucursalId && $product->has_stock && !$product->is_global) {
                $stock = $product->getStockForBranch($this->selectedSucursalId);
                $product->stockPorSucursal = $stock ? $stock->stockActual : 0;
                $product->estadoStockSucursal = $stock ? $stock->estado_stock : 'agotado';
                $product->stockPorSucursales = null;
            } else {
                $product->stockPorSucursal = $product->has_stock ? $product->stockActual : null;
                $product->estadoStockSucursal = $product->has_stock ? $product->estado_stock : null;
                $product->stockPorSucursales = null;
            }
            return $product;
        });

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