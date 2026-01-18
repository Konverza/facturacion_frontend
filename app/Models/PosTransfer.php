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
        'es_devolucion',
        'requiere_liquidacion',
        'liquidacion_completada',
        'observaciones_liquidacion',
        'numero_transferencia',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'fecha_traslado' => 'datetime',
        'es_devolucion' => 'boolean',
        'requiere_liquidacion' => 'boolean',
        'liquidacion_completada' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($transfer) {
            if (empty($transfer->numero_transferencia)) {
                $transfer->numero_transferencia = self::generarNumeroTransferencia();
            }
        });
    }

    /**
     * Relación con el producto (legacy - para transferencias antiguas)
     */
    public function businessProduct()
    {
        return $this->belongsTo(BusinessProduct::class, 'business_product_id');
    }

    /**
     * Relación con los items de la transferencia
     */
    public function items()
    {
        return $this->hasMany(PosTransferItem::class, 'pos_transfer_id');
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
     * Generar número de transferencia único
     */
    public static function generarNumeroTransferencia(): string
    {
        $fecha = now()->format('Ymd');
        $ultimo = self::where('numero_transferencia', 'like', "TRF-{$fecha}%")
            ->orderBy('numero_transferencia', 'desc')
            ->first();
        
        $secuencia = 1;
        if ($ultimo) {
            $ultimoNumero = substr($ultimo->numero_transferencia, -4);
            $secuencia = intval($ultimoNumero) + 1;
        }
        
        return sprintf('TRF-%s-%04d', $fecha, $secuencia);
    }

    /**
     * Verificar si es transferencia múltiple
     */
    public function esTransferenciaMultiple(): bool
    {
        return $this->items()->count() > 0;
    }

    /**
     * Obtener cantidad total de items
     */
    public function getCantidadTotalAttribute(): float
    {
        if ($this->esTransferenciaMultiple()) {
            return $this->items->sum('cantidad_solicitada');
        }
        return (float) $this->cantidad ?? 0;
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

        // Si es transferencia múltiple, ejecutar por items
        if ($this->esTransferenciaMultiple()) {
            return $this->ejecutarTransferenciaMultiple();
        }

        // Si es transferencia simple (legacy)
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
     * Ejecutar transferencia múltiple
     */
    private function ejecutarTransferenciaMultiple(): bool
    {
        foreach ($this->items as $item) {
            $producto = $item->businessProduct;

            if ($producto->is_global) {
                throw new \Exception("El producto {$producto->descripcion} es global y no se puede trasladar.");
            }

            if (!$producto->has_stock) {
                throw new \Exception("El producto {$producto->descripcion} no tiene control de stock habilitado.");
            }

            switch ($this->tipo_traslado) {
                case 'branch_to_pos':
                    $this->ejecutarItemBranchToPos($item);
                    break;
                case 'pos_to_branch':
                    $this->ejecutarItemPosToBranch($item);
                    break;
                case 'pos_to_pos':
                    $this->ejecutarItemPosToPos($item);
                    break;
                default:
                    throw new \Exception('Tipo de traslado no válido.');
            }
        }

        $this->estado = 'completado';
        $this->save();

        return true;
    }

    /**
     * Ejecutar item de transferencia branch to pos
     */
    private function ejecutarItemBranchToPos(PosTransferItem $item): void
    {
        $stockOrigen = BranchProductStock::firstOrCreate(
            [
                'business_product_id' => $item->business_product_id,
                'sucursal_id' => $this->sucursal_origen_id,
            ],
            [
                'stockActual' => 0,
                'stockMinimo' => 0,
                'estado_stock' => 'agotado',
            ]
        );

        if ($stockOrigen->stockActual < $item->cantidad_solicitada) {
            throw new \Exception("Stock insuficiente para {$item->businessProduct->descripcion}. Disponible: {$stockOrigen->stockActual}");
        }

        $stockOrigen->reducirStock((float) $item->cantidad_solicitada);

        $stockDestino = PosProductStock::firstOrCreate(
            [
                'business_product_id' => $item->business_product_id,
                'punto_venta_id' => $this->punto_venta_destino_id,
            ],
            [
                'stockActual' => 0,
                'stockMinimo' => $stockOrigen->stockMinimo,
                'estado_stock' => 'disponible',
            ]
        );

        $stockDestino->aumentarStock((float) $item->cantidad_solicitada);

        $this->registrarMovimientoItem($item, 'Traslado de sucursal a punto de venta');
    }

    /**
     * Ejecutar item de transferencia pos to branch
     */
    private function ejecutarItemPosToBranch(PosTransferItem $item): void
    {
        $stockOrigen = PosProductStock::firstOrCreate(
            [
                'business_product_id' => $item->business_product_id,
                'punto_venta_id' => $this->punto_venta_origen_id,
            ],
            [
                'stockActual' => 0,
                'stockMinimo' => 0,
                'estado_stock' => 'agotado',
            ]
        );

        if ($stockOrigen->stockActual < $item->cantidad_solicitada) {
            throw new \Exception("Stock insuficiente para {$item->businessProduct->descripcion}. Disponible: {$stockOrigen->stockActual}");
        }

        $stockOrigen->reducirStock((float) $item->cantidad_solicitada);

        $stockDestino = BranchProductStock::firstOrCreate(
            [
                'business_product_id' => $item->business_product_id,
                'sucursal_id' => $this->sucursal_destino_id,
            ],
            [
                'stockActual' => 0,
                'stockMinimo' => $stockOrigen->stockMinimo,
                'estado_stock' => 'disponible',
            ]
        );

        $stockDestino->aumentarStock((float) $item->cantidad_solicitada);

        $this->registrarMovimientoItem($item, 'Traslado de punto de venta a sucursal');
    }

    /**
     * Ejecutar item de transferencia pos to pos
     */
    private function ejecutarItemPosToPos(PosTransferItem $item): void
    {
        $stockOrigen = PosProductStock::firstOrCreate(
            [
                'business_product_id' => $item->business_product_id,
                'punto_venta_id' => $this->punto_venta_origen_id,
            ],
            [
                'stockActual' => 0,
                'stockMinimo' => 0,
                'estado_stock' => 'agotado',
            ]
        );

        if ($stockOrigen->stockActual < $item->cantidad_solicitada) {
            throw new \Exception("Stock insuficiente para {$item->businessProduct->descripcion}. Disponible: {$stockOrigen->stockActual}");
        }

        $stockOrigen->reducirStock((float) $item->cantidad_solicitada);

        $stockDestino = PosProductStock::firstOrCreate(
            [
                'business_product_id' => $item->business_product_id,
                'punto_venta_id' => $this->punto_venta_destino_id,
            ],
            [
                'stockActual' => 0,
                'stockMinimo' => $stockOrigen->stockMinimo,
                'estado_stock' => 'disponible',
            ]
        );

        $stockDestino->aumentarStock((float) $item->cantidad_solicitada);

        $this->registrarMovimientoItem($item, 'Traslado entre puntos de venta');
    }

    /**
     * Registrar movimiento para un item específico
     */
    private function registrarMovimientoItem(PosTransferItem $item, string $descripcion): void
    {
        $tipo = match($this->tipo_traslado) {
            'branch_to_pos' => 'salida',
            'pos_to_branch' => 'entrada',
            'pos_to_pos' => 'salida',
            default => 'salida'
        };

        BusinessProductMovement::create([
            'business_product_id' => $item->business_product_id,
            'numero_factura' => $this->numero_transferencia,
            'tipo' => $tipo,
            'cantidad' => $item->cantidad_solicitada,
            'precio_unitario' => $item->businessProduct->precioUni,
            'producto' => $item->businessProduct->descripcion,
            'descripcion' => $descripcion . ($item->nota_item ? ' - ' . $item->nota_item : ''),
        ]);
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
