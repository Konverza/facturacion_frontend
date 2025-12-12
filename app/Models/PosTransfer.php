<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosTransfer extends Model
{
    protected $fillable = [
        'business_product_id',
        'sucursal_origen_id',
        'punto_venta_origen_id',
        'sucursal_destino_id',
        'punto_venta_destino_id',
        'tipo_traslado',
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
     * Relación con el punto de venta de origen
     */
    public function puntoVentaOrigen()
    {
        return $this->belongsTo(PuntoVenta::class, 'punto_venta_origen_id');
    }

    /**
     * Relación con la sucursal de destino
     */
    public function sucursalDestino()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_destino_id');
    }

    /**
     * Relación con el punto de venta de destino
     */
    public function puntoVentaDestino()
    {
        return $this->belongsTo(PuntoVenta::class, 'punto_venta_destino_id');
    }

    /**
     * Relación con el usuario que realizó el traslado
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener nombre del origen
     */
    public function getOrigenNombreAttribute(): string
    {
        if ($this->tipo_traslado === 'branch_to_pos' || $this->tipo_traslado === 'pos_to_pos') {
            return $this->sucursalOrigen ? $this->sucursalOrigen->nombre : 'N/A';
        }
        return $this->puntoVentaOrigen ? $this->puntoVentaOrigen->nombre : 'N/A';
    }

    /**
     * Obtener nombre del destino
     */
    public function getDestinoNombreAttribute(): string
    {
        if ($this->tipo_traslado === 'pos_to_branch') {
            return $this->sucursalDestino ? $this->sucursalDestino->nombre : 'N/A';
        }
        return $this->puntoVentaDestino ? $this->puntoVentaDestino->nombre : 'N/A';
    }

    /**
     * Ejecutar el traslado según el tipo
     * 
     * @throws \Exception si no hay suficiente stock o configuración incorrecta
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

        switch ($this->tipo_traslado) {
            case 'branch_to_pos':
                return $this->ejecutarBranchToPos();
            case 'pos_to_branch':
                return $this->ejecutarPosToBranch();
            case 'pos_to_pos':
                return $this->ejecutarPosToPos();
            default:
                throw new \Exception('Tipo de traslado no válido.');
        }
    }

    /**
     * Traslado de sucursal a punto de venta
     */
    private function ejecutarBranchToPos(): bool
    {
        // Obtener stock de sucursal origen
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
            throw new \Exception("Stock insuficiente en sucursal. Disponible: {$stockOrigen->stockActual}");
        }

        // Reducir en sucursal
        $stockOrigen->reducirStock((float) $this->cantidad);

        // Obtener o crear stock en punto de venta destino
        $stockDestino = PosProductStock::firstOrCreate(
            [
                'business_product_id' => $this->business_product_id,
                'punto_venta_id' => $this->punto_venta_destino_id,
            ],
            [
                'stockActual' => 0,
                'stockMinimo' => $stockOrigen->stockMinimo,
                'estado_stock' => 'disponible',
            ]
        );

        // Aumentar en punto de venta
        $stockDestino->aumentarStock((float) $this->cantidad);

        // Registrar movimiento
        $this->registrarMovimiento('Traslado de sucursal a punto de venta');

        // Marcar como completado
        $this->estado = 'completado';
        $this->save();

        return true;
    }

    /**
     * Traslado de punto de venta a sucursal
     */
    private function ejecutarPosToBranch(): bool
    {
        // Obtener stock de punto de venta origen
        $stockOrigen = PosProductStock::firstOrCreate(
            [
                'business_product_id' => $this->business_product_id,
                'punto_venta_id' => $this->punto_venta_origen_id,
            ],
            [
                'stockActual' => 0,
                'stockMinimo' => 0,
                'estado_stock' => 'agotado',
            ]
        );

        // Verificar disponibilidad
        if ($stockOrigen->stockActual < $this->cantidad) {
            throw new \Exception("Stock insuficiente en punto de venta. Disponible: {$stockOrigen->stockActual}");
        }

        // Reducir en punto de venta
        $stockOrigen->reducirStock((float) $this->cantidad);

        // Obtener o crear stock en sucursal destino
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

        // Aumentar en sucursal
        $stockDestino->aumentarStock((float) $this->cantidad);

        // Registrar movimiento
        $this->registrarMovimiento('Traslado de punto de venta a sucursal');

        // Marcar como completado
        $this->estado = 'completado';
        $this->save();

        return true;
    }

    /**
     * Traslado entre puntos de venta
     */
    private function ejecutarPosToPos(): bool
    {
        // Obtener stock de punto de venta origen
        $stockOrigen = PosProductStock::firstOrCreate(
            [
                'business_product_id' => $this->business_product_id,
                'punto_venta_id' => $this->punto_venta_origen_id,
            ],
            [
                'stockActual' => 0,
                'stockMinimo' => 0,
                'estado_stock' => 'agotado',
            ]
        );

        // Verificar disponibilidad
        if ($stockOrigen->stockActual < $this->cantidad) {
            throw new \Exception("Stock insuficiente en punto de venta origen. Disponible: {$stockOrigen->stockActual}");
        }

        // Reducir en origen
        $stockOrigen->reducirStock((float) $this->cantidad);

        // Obtener o crear stock en punto de venta destino
        $stockDestino = PosProductStock::firstOrCreate(
            [
                'business_product_id' => $this->business_product_id,
                'punto_venta_id' => $this->punto_venta_destino_id,
            ],
            [
                'stockActual' => 0,
                'stockMinimo' => $stockOrigen->stockMinimo,
                'estado_stock' => 'disponible',
            ]
        );

        // Aumentar en destino
        $stockDestino->aumentarStock((float) $this->cantidad);

        // Registrar movimiento
        $this->registrarMovimiento('Traslado entre puntos de venta');

        // Marcar como completado
        $this->estado = 'completado';
        $this->save();

        return true;
    }

    /**
     * Registrar movimiento en el historial
     */
    private function registrarMovimiento(string $descripcion): void
    {
        // Determinar tipo de movimiento basado en el tipo de traslado
        // Para branch_to_pos: salida de sucursal
        // Para pos_to_branch: entrada a sucursal
        // Para pos_to_pos: se considera salida del origen
        $tipo = match($this->tipo_traslado) {
            'branch_to_pos' => 'salida',
            'pos_to_branch' => 'entrada',
            'pos_to_pos' => 'salida',
            default => 'salida'
        };

        BusinessProductMovement::create([
            'business_product_id' => $this->business_product_id,
            'numero_factura' => 'TRASLADO-' . $this->id,
            'tipo' => $tipo,
            'cantidad' => $this->cantidad,
            'precio_unitario' => $this->businessProduct->precioUni,
            'producto' => $this->businessProduct->descripcion,
            'descripcion' => $descripcion . ' - ' . $this->notas,
        ]);
    }
}
