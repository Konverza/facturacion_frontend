<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PuntoVenta extends Model
{
    protected $table = 'punto_ventas';

    protected $fillable = [
        'nombre',
        'codPuntoVenta',
        'sucursal_id',
        'has_independent_inventory',
    ];

    protected $casts = [
        'has_independent_inventory' => 'boolean',
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    /**
     * Relación con stocks de productos
     */
    public function productStocks()
    {
        return $this->hasMany(PosProductStock::class, 'punto_venta_id');
    }

    /**
     * Obtener stock de un producto específico
     */
    public function getStockForProduct($productId)
    {
        return $this->productStocks()->where('business_product_id', $productId)->first();
    }

    /**
     * Verificar si el punto de venta puede manejar inventario independiente
     */
    public function canHaveInventory(): bool
    {
        return $this->sucursal->business->pos_inventory_enabled && $this->has_independent_inventory;
    }
}
