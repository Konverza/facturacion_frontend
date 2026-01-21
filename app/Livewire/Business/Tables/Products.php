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
    public $defaultPosId = null;
    public $userOnlyDefaultPos = false;
    public $posHasIndependentInventory = false;
    public $priceVariantsEnabled = false;
    public $showSpecialPrices = false;

    public $options = [
        'todos' => 'Todos',
        'disponible' => 'Disponible',
        'por_agotarse' => 'Por agotarse',
        'agotado' => 'Agotado',
        'n/a' => 'Sin Inventario',
    ];

    public function mount()
    {
        $business = \App\Models\Business::find(session('business'));
        $this->priceVariantsEnabled = (bool) ($business?->price_variants_enabled);
        $this->showSpecialPrices = (bool) ($business?->show_special_prices);

        // Obtener configuración del usuario
        $businessUser = BusinessUser::where('business_id', session('business'))
            ->where('user_id', auth()->id())
            ->first();

        if ($businessUser) {
            $this->canSelectBranch = (bool) $businessUser->branch_selector;
            $this->userOnlyDefaultPos = (bool) $businessUser->only_default_pos;

            // Obtener sucursal por defecto desde el POS
            if ($businessUser->default_pos_id) {
                $pos = $businessUser->defaultPos;
                if ($pos && $pos->sucursal_id) {
                    $this->defaultPosId = $pos->id;
                    $this->defaultSucursalId = $pos->sucursal_id;
                    $this->selectedSucursalId = $this->defaultSucursalId;
                    $this->posHasIndependentInventory = (bool) $pos->has_independent_inventory;
                    
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

        // Caso 1: Usuario con POS por defecto que tiene inventario independiente - solo productos del POS
        if ($this->userOnlyDefaultPos && $this->defaultPosId && $this->posHasIndependentInventory) {
            $posId = $this->defaultPosId;
            $query->where(function ($q) use ($posId) {
                // Productos globales (siempre disponibles)
                $q->where('is_global', true)
                  // O productos con stock en el punto de venta
                  ->orWhereHas('posStocks', function ($stockQuery) use ($posId) {
                      $stockQuery->where('punto_venta_id', $posId);
                  })
                  // O productos sin control de stock
                  ->orWhere('has_stock', false);
            });
        }
        // Caso 2: Filtrar por sucursal seleccionada si aplica (incluyendo agotados)
        // No filtrar si está en modo "ver todas"
        elseif ($this->selectedSucursalId && !$this->viewAllBranches) {
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

        // Filtrar por estado de stock
        if ($this->stockState && $this->stockState !== 'todos') {
            if ($this->stockState === 'n/a') {
                $query->where(function ($q) {
                    $q->where('has_stock', false)
                      ->orWhere('is_global', true);
                });
            } else {
                // Filtrar por estado de stock según contexto (POS o Sucursal)
                if ($this->userOnlyDefaultPos && $this->defaultPosId && $this->posHasIndependentInventory) {
                    // Filtrar por stock en el POS
                    $query->whereHas('posStocks', function ($stockQuery) {
                        $stockQuery->where('punto_venta_id', $this->defaultPosId)
                                   ->where('estado_stock', $this->stockState);
                    });
                } elseif ($this->selectedSucursalId && !$this->viewAllBranches) {
                    // Filtrar por stock en la sucursal
                    $query->whereHas('branchStocks', function ($stockQuery) {
                        $stockQuery->where('sucursal_id', $this->selectedSucursalId)
                                   ->where('estado_stock', $this->stockState);
                    });
                }
            }
        }

        $products = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        // Agregar información de stock por sucursal a cada producto
        $products->getCollection()->transform(function ($product) {
            // Caso 1: Usuario con POS por defecto que tiene inventario independiente
            if ($this->userOnlyDefaultPos && $this->defaultPosId && $this->posHasIndependentInventory) {
                if ($product->has_stock && !$product->is_global) {
                    $stock = $product->getStockForPos($this->defaultPosId);
                    $product->stockPorSucursal = $stock ? $stock->stockActual : 0;
                    $product->estadoStockSucursal = $stock ? $stock->estado_stock : 'agotado';
                } else {
                    $product->stockPorSucursal = $product->has_stock ? $product->stockActual : null;
                    $product->estadoStockSucursal = $product->has_stock ? $product->estado_stock : null;
                }
                $product->stockPorSucursales = null;
                $product->stockPorPOS = collect([]);
            }
            // Caso 2: Modo "ver todas": mostrar inventario global por sucursal
            elseif ($this->viewAllBranches) {
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
                
                // Agregar stocks de puntos de venta con inventario independiente
                $product->stockPorPOS = $product->posStocks()
                    ->with('puntoVenta:id,nombre,sucursal_id')
                    ->whereHas('puntoVenta', function($query) {
                        $query->where('has_independent_inventory', true);
                    })
                    ->get()
                    ->map(function ($stock) {
                        return [
                            'pos_id' => $stock->punto_venta_id,
                            'pos_nombre' => $stock->puntoVenta->nombre ?? 'Sin nombre',
                            'sucursal_id' => $stock->puntoVenta->sucursal_id ?? null,
                            'stock' => $stock->stockActual,
                            'estado' => $stock->estado_stock,
                        ];
                    });
            }
            // Caso 3: Sucursal seleccionada con stock
            elseif ($this->selectedSucursalId && $product->has_stock && !$product->is_global) {
                $stock = $product->getStockForBranch($this->selectedSucursalId);
                $product->stockPorSucursal = $stock ? $stock->stockActual : 0;
                $product->estadoStockSucursal = $stock ? $stock->estado_stock : 'agotado';
                $product->stockPorSucursales = null;
                
                // Agregar stocks de puntos de venta con inventario independiente de esta sucursal
                $product->stockPorPOS = $product->posStocks()
                    ->with('puntoVenta:id,nombre,sucursal_id')
                    ->whereHas('puntoVenta', function($query) {
                        $query->where('has_independent_inventory', true)
                              ->where('sucursal_id', $this->selectedSucursalId);
                    })
                    ->get()
                    ->map(function ($stock) {
                        return [
                            'pos_id' => $stock->punto_venta_id,
                            'pos_nombre' => $stock->puntoVenta->nombre ?? 'Sin nombre',
                            'stock' => $stock->stockActual,
                            'estado' => $stock->estado_stock,
                        ];
                    });
            }
            // Caso 4: Sin sucursal seleccionada o producto global/sin stock
            else {
                $product->stockPorSucursal = $product->has_stock ? $product->stockActual : null;
                $product->estadoStockSucursal = $product->has_stock ? $product->estado_stock : null;
                $product->stockPorSucursales = null;
                $product->stockPorPOS = collect([]);
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

        // Obtener unidades de medida para la exportación
        $octopusService = new \App\Services\OctopusService();
        $unidadesMedidas = $octopusService->getCatalog("CAT-014");

        // Obtener información del negocio
        $business = \App\Models\Business::find(session("business"));

        // Determinar la sucursal a exportar
        $sucursalId = null;
        if (!$this->viewAllBranches && $this->selectedSucursalId) {
            $sucursalId = $this->selectedSucursalId;
        }

        return Excel::download(
            new ProductsExport(
                $this->search,
                $this->searchName,
                $this->searchCode,
                $this->exactSearch,
                $this->stockState,
                $sucursalId,
                $unidadesMedidas,
                $business
            ),
            $fileName
        );
    }
}