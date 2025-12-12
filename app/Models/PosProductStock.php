<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosProductStock extends Model
{
    protected $table = 'pos_product_stock';

    protected $fillable = [
        'business_product_id',
        'punto_venta_id',
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
     * Relación con el punto de venta
     */
    public function puntoVenta()
    {
        return $this->belongsTo(PuntoVenta::class, 'punto_venta_id');
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
            $this->stockActual = (float)$this->stockActual - $cantidad;
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
        $this->stockActual = (float)$this->stockActual + $cantidad;
        $this->updateStockEstado();
    }
}
