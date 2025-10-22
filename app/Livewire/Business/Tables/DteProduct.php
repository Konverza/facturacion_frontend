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

    // Nueva propiedad para selección de sucursal
    public $selectedSucursalId = null;
    public $availableSucursales = [];
    public $canSelectBranch = false;
    public $defaultSucursalId = null;

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
                    $this->availableSucursales = Sucursal::find($this->defaultSucursalId)
                        ?->pluck('nombre', 'id')
                        ->toArray();
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

        // Filtrar por sucursal seleccionada si aplica
        if ($this->selectedSucursalId) {
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

        // Agregar información de stock por sucursal a cada producto
        $products->getCollection()->transform(function ($product) {
            if ($this->selectedSucursalId && $product->has_stock && !$product->is_global) {
                $stock = $product->getStockForBranch($this->selectedSucursalId);
                $product->stockPorSucursal = $stock ? $stock->stockActual : 0;
                $product->estadoStockSucursal = $stock ? $stock->estado_stock : 'agotado';
            } else {
                $product->stockPorSucursal = $product->has_stock ? $product->stockActual : null;
                $product->estadoStockSucursal = $product->has_stock ? $product->estado_stock : null;
            }
            return $product;
        });

        return view('livewire.business.tables.dte-product', [
            'products' => $products,
        ]);
    }
}

