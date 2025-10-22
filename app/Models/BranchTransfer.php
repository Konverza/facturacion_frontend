<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchTransfer extends Model
{
    protected $fillable = [
        'business_product_id',
        'sucursal_origen_id',
        'sucursal_destino_id',
        'cantidad',
        'user_id',
        'notas',
        'estado',
        'fecha_traslado',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'fecha_traslado' => 'datetime',
    ];

    /**
     * Relación con el producto
     */
    public function businessProduct()
    {
        return $this->belongsTo(BusinessProduct::class, 'business_product_id');
    }

    /**
     * Relación con la sucursal de origen
     */
    public function sucursalOrigen()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_origen_id');
    }

    /**
     * Relación con la sucursal de destino
     */
    public function sucursalDestino()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_destino_id');
    }

    /**
     * Relación con el usuario que realizó el traslado
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Ejecutar el traslado de productos entre sucursales
     * Reduce stock en origen y aumenta en destino
     * 
     * @throws \Exception si no hay suficiente stock en origen
     */
    public function ejecutar(): bool
    {
        if ($this->estado === 'completado') {
            throw new \Exception('El traslado ya fue completado.');
        }

        $producto = $this->businessProduct;
        
        // No se pueden trasladar productos globales
        if ($producto->is_global) {
            throw new \Exception('No se pueden trasladar productos globales.');
        }

        // Verificar que el producto tenga control de stock
        if (!$producto->has_stock) {
            throw new \Exception('El producto no tiene control de stock habilitado.');
        }

        // Obtener o crear stock en origen
        $stockOrigen = BranchProductStock::firstOrCreate(
            [
                'business_product_id' => $this->business_product_id,
                'sucursal_id' => $this->sucursal_origen_id,
            ],
            [
                'stockActual' => 0,
                'stockMinimo' => 0,
                'estado_stock' => 'agotado',
            ]
        );

        // Verificar disponibilidad
        if ($stockOrigen->stockActual < $this->cantidad) {
            throw new \Exception("Stock insuficiente en sucursal origen. Disponible: {$stockOrigen->stockActual}");
        }

        // Reducir en origen
        $stockOrigen->reducirStock((float) $this->cantidad);

        // Obtener o crear stock en destino
        $stockDestino = BranchProductStock::firstOrCreate(
            [
                'business_product_id' => $this->business_product_id,
                'sucursal_id' => $this->sucursal_destino_id,
            ],
            [
                'stockActual' => 0,
                'stockMinimo' => $stockOrigen->stockMinimo,
                'estado_stock' => 'disponible',
            ]
        );

        // Aumentar en destino
        $stockDestino->aumentarStock((float) $this->cantidad);

        // Marcar traslado como completado
        $this->estado = 'completado';
        $this->save();

        return true;
    }
}
