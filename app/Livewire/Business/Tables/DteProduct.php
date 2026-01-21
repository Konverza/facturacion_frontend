<?php

namespace App\Livewire\Business\Tables;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BusinessProduct;
use App\Models\BusinessUser;
use App\Models\Sucursal;

class DteProduct extends Component
{
    use WithPagination;

    public $perPage = 5;
    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';

    public $dte;
    public $number;

    // Nueva propiedad para selección de sucursal y POS
    public $selectedSucursalId = null;
    public $selectedPosId = null;
    public $availableSucursales = [];
    public $canSelectBranch = false;
    public $defaultSucursalId = null;
    public $defaultPosId = null;
    public $userOnlyDefaultPos = false;
    public $posHasIndependentInventory = false;
    public $priceVariantsEnabled = false;

    public function mount()
    {
        $business = \App\Models\Business::find(session('business'));
        $this->priceVariantsEnabled = (bool) ($business?->price_variants_enabled);

        // Obtener configuración del usuario
        $businessUser = BusinessUser::where('business_id', session('business'))
            ->where('user_id', auth()->id())
            ->first();

        if ($businessUser) {
            $this->canSelectBranch = (bool) $businessUser->branch_selector;
            $this->userOnlyDefaultPos = (bool) $businessUser->only_default_pos;

            // Obtener POS por defecto
            if ($businessUser->default_pos_id) {
                $pos = $businessUser->defaultPos;
                if ($pos) {
                    $this->defaultPosId = $pos->id;
                    $this->selectedPosId = $pos->id;
                    $this->defaultSucursalId = $pos->sucursal_id;
                    $this->selectedSucursalId = $pos->sucursal_id;
                    $this->posHasIndependentInventory = (bool) $pos->has_independent_inventory;
                    
                    // Si el usuario solo puede usar su POS por defecto
                    if ($this->userOnlyDefaultPos) {
                        $this->availableSucursales = Sucursal::find($this->defaultSucursalId)
                            ?->pluck('nombre', 'id')
                            ->toArray();
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

            // Si no tiene POS por defecto ni sucursal seleccionada, asegurar que haya una sucursal
            if (!$this->selectedSucursalId) {
                // Si no hay sucursales disponibles cargadas, cargar todas
                if (empty($this->availableSucursales)) {
                    $this->availableSucursales = Sucursal::where('business_id', session('business'))
                        ->orderBy('nombre')
                        ->get()
                        ->pluck('nombre', 'id')
                        ->toArray();
                }
                
                // Seleccionar la primera sucursal disponible
                if (!empty($this->availableSucursales)) {
                    $this->selectedSucursalId = array_key_first($this->availableSucursales);
                }
            }
        }
    }

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
        $query = BusinessProduct::where("business_id", session("business"));

        // Caso 1: Usuario con POS por defecto que tiene inventario independiente
        if ($this->userOnlyDefaultPos && $this->selectedPosId && $this->posHasIndependentInventory) {
            $query->availableInPos($this->selectedPosId);
        }
        // Caso 2: Filtrar por sucursal seleccionada si aplica
        elseif ($this->selectedSucursalId) {
            $query->availableInBranch($this->selectedSucursalId);
        }

        // Aplicar búsqueda
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('codigo', 'like', "%{$this->search}%")
                    ->orWhere('descripcion', 'like', "%{$this->search}%");
            });
        }

        $products = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        // Agregar información de stock según contexto (POS o Sucursal)
        $products->getCollection()->transform(function ($product) {
            if ($product->has_stock) {
                // Caso 1: Usuario con POS por defecto que tiene inventario independiente
                if ($this->userOnlyDefaultPos && $this->selectedPosId && $this->posHasIndependentInventory) {
                    $stock = $product->getStockForPos($this->selectedPosId);
                    $product->stockPorSucursal = $stock ? $stock->stockActual : 0;
                    $product->estadoStockSucursal = $stock ? $stock->estado_stock : 'agotado';
                    $product->inventorySource = 'pos';
                }
                // Caso 2: Stock por sucursal
                elseif ($this->selectedSucursalId && !$product->is_global) {
                    $stock = $product->getStockForBranch($this->selectedSucursalId);
                    $product->stockPorSucursal = $stock ? $stock->stockActual : 0;
                    $product->estadoStockSucursal = $stock ? $stock->estado_stock : 'agotado';
                    $product->inventorySource = 'branch';
                }
                // Caso 3: Producto global
                else {
                    $product->stockPorSucursal = $product->stockActual;
                    $product->estadoStockSucursal = $product->estado_stock;
                    $product->inventorySource = 'global';
                }
            } else {
                $product->stockPorSucursal = null;
                $product->estadoStockSucursal = null;
                $product->inventorySource = 'none';
            }
            return $product;
        });

        return view('livewire.business.tables.dte-product', [
            'products' => $products,
        ]);
    }
}

