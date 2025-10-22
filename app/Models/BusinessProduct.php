<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessProduct extends Model
{
    use HasFactory;

    protected $table = 'business_product';

    protected $fillable = [
        'business_id',
        'tipoItem',
        'codigo',
        'uniMedida',
        'descripcion',
        'precioUni',
        'special_price',
        'special_price_with_iva',
        'cost',
        'margin',
        'precioSinTributos',
        'tributos',
        'stockInicial',
        'stockActual',
        'stockMinimo',
        'estado_stock',
        'has_stock',
        'is_global',
        'image_url',
        'category_id',
    ];

    protected $casts = [
        'has_stock' => 'boolean',
        'is_global' => 'boolean',
        'tributos' => 'array',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function movements()
    {
        return $this->hasMany(BusinessProductMovement::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    /**
     * Relación con stocks por sucursal
     */
    public function branchStocks()
    {
        return $this->hasMany(BranchProductStock::class, 'business_product_id');
    }

    /**
     * Obtener stock de una sucursal específica
     */
    public function getStockForBranch($sucursalId)
    {
        return $this->branchStocks()->where('sucursal_id', $sucursalId)->first();
    }

    /**
     * Obtener stock disponible para una sucursal
     * Si es global, retorna null (sin control de stock)
     * Si no tiene stock habilitado, retorna null
     */
    public function getAvailableStockForBranch($sucursalId): ?float
    {
        if ($this->is_global || !$this->has_stock) {
            return null; // Sin control de stock
        }

        $stock = $this->getStockForBranch($sucursalId);
        return $stock ? (float) $stock->stockActual : 0;
    }

    /**
     * Verificar si hay suficiente stock en una sucursal
     */
    public function hasEnoughStockInBranch($sucursalId, float $cantidad): bool
    {
        // Productos globales o sin control de stock siempre están disponibles
        if ($this->is_global || !$this->has_stock) {
            return true;
        }

        $disponible = $this->getAvailableStockForBranch($sucursalId);
        return $disponible !== null && $disponible >= $cantidad;
    }

    /**
     * Reducir stock de una sucursal
     */
    public function reduceStockInBranch($sucursalId, float $cantidad, string $numeroFactura, string $descripcion = 'Venta de producto'): bool
    {
        if ($this->is_global || !$this->has_stock) {
            return true; // Sin control de stock
        }

        $stock = $this->getStockForBranch($sucursalId);
        
        if (!$stock) {
            throw new \Exception("El producto no tiene stock registrado en esta sucursal.");
        }

        if (!$stock->reducirStock($cantidad)) {
            return false;
        }

        // Registrar movimiento
        BusinessProductMovement::create([
            'business_product_id' => $this->id,
            'numero_factura' => $numeroFactura,
            'tipo' => 'salida',
            'cantidad' => $cantidad,
            'precio_unitario' => $this->precioUni,
            'producto' => $this->descripcion,
            'descripcion' => $descripcion,
        ]);

        return true;
    }

    /**
     * Aumentar stock de una sucursal
     */
    public function increaseStockInBranch($sucursalId, float $cantidad, string $numeroFactura, string $descripcion = 'Entrada de producto'): void
    {
        if ($this->is_global || !$this->has_stock) {
            return; // Sin control de stock
        }

        $stock = BranchProductStock::firstOrCreate(
            [
                'business_product_id' => $this->id,
                'sucursal_id' => $sucursalId,
            ],
            [
                'stockActual' => 0,
                'stockMinimo' => $this->stockMinimo ?? 0,
                'estado_stock' => 'disponible',
            ]
        );

        $stock->aumentarStock($cantidad);

        // Registrar movimiento
        BusinessProductMovement::create([
            'business_product_id' => $this->id,
            'numero_factura' => $numeroFactura,
            'tipo' => 'entrada',
            'cantidad' => $cantidad,
            'precio_unitario' => $this->precioUni,
            'producto' => $this->descripcion,
            'descripcion' => $descripcion,
        ]);
    }

    /**
     * Scope: Productos disponibles en una sucursal específica
     * Incluye productos globales y productos con stock en esa sucursal
     * Excluye productos agotados
     */
    public function scopeAvailableInBranch($query, $sucursalId)
    {
        return $query->where(function ($q) use ($sucursalId) {
            // Productos globales (siempre disponibles)
            $q->where('is_global', true)
              // O productos con stock disponible en la sucursal
              ->orWhereHas('branchStocks', function ($stockQuery) use ($sucursalId) {
                  $stockQuery->where('sucursal_id', $sucursalId)
                             ->whereIn('estado_stock', ['disponible', 'por_agotarse']);
              })
              // O productos sin control de stock
              ->orWhere('has_stock', false);
        });
    }
}
