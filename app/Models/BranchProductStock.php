<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchProductStock extends Model
{
    protected $table = 'business_product_stock';

    protected $fillable = [
        'business_product_id',
        'sucursal_id',
        'stockActual',
        'stockMinimo',
        'estado_stock',
    ];

    protected $casts = [
        'stockActual' => 'decimal:2',
        'stockMinimo' => 'decimal:2',
    ];

    /**
     * Relación con el producto
     */
    public function businessProduct()
    {
        return $this->belongsTo(BusinessProduct::class, 'business_product_id');
    }

    /**
     * Relación con la sucursal
     */
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    /**
     * Actualizar estado de stock basado en stockActual y stockMinimo
     */
    public function updateStockEstado(): void
    {
        if ($this->stockActual <= 0) {
            $this->estado_stock = 'agotado';
        } elseif ($this->stockActual <= $this->stockMinimo) {
            $this->estado_stock = 'por_agotarse';
        } else {
            $this->estado_stock = 'disponible';
        }
        $this->save();
    }

    /**
     * Reducir stock (para ventas)
     */
    public function reducirStock(float $cantidad): bool
    {
        if ($this->stockActual >= $cantidad) {
            $this->stockActual -= $cantidad;
            $this->updateStockEstado();
            return true;
        }
        return false;
    }

    /**
     * Aumentar stock (para anulaciones/entradas)
     */
    public function aumentarStock(float $cantidad): void
    {
        $this->stockActual += $cantidad;
        $this->updateStockEstado();
    }
}
