<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Migración de datos para inicializar el sistema de inventario por punto de venta.
     * 
     * Esta migración es OPCIONAL y sirve para:
     * 1. Copiar el stock actual de productos globales a inventario por sucursal
     * 2. Preparar productos para el sistema de inventario por POS
     * 
     * ADVERTENCIA: Esta migración modifica datos existentes.
     * Hacer backup antes de ejecutar.
     */
    public function up(): void
    {
        // PASO 1: Identificar productos que deberían tener control de stock
        // (Productos con stockActual > 0 o stockInicial > 0)
        $productosConStock = DB::table('business_product')
            ->where(function ($query) {
                $query->where('stockActual', '>', 0)
                      ->orWhere('stockInicial', '>', 0);
            })
            ->where('is_global', false)
            ->where('has_stock', true)
            ->get();

        echo "Encontrados " . $productosConStock->count() . " productos con stock para migrar\n";

        // PASO 2: Para cada producto, crear registro de stock por sucursal
        // si es que el negocio tiene sucursales y no existe ya
        foreach ($productosConStock as $producto) {
            // Obtener sucursales del negocio
            $sucursales = DB::table('sucursals')
                ->where('business_id', $producto->business_id)
                ->get();

            foreach ($sucursales as $sucursal) {
                // Verificar si ya existe registro de stock para esta sucursal
                $existeStock = DB::table('business_product_stock')
                    ->where('business_product_id', $producto->id)
                    ->where('sucursal_id', $sucursal->id)
                    ->exists();

                if (!$existeStock) {
                    // Crear registro de stock inicial
                    // Por defecto, asignamos todo el stock a la primera sucursal
                    // o distribuimos equitativamente
                    $stockPorSucursal = $producto->stockActual / $sucursales->count();
                    
                    DB::table('business_product_stock')->insert([
                        'business_product_id' => $producto->id,
                        'sucursal_id' => $sucursal->id,
                        'stockActual' => $stockPorSucursal,
                        'stockMinimo' => $producto->stockMinimo ?? 0,
                        'estado_stock' => $stockPorSucursal > 0 ? 'disponible' : 'agotado',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    echo "Creado stock para producto {$producto->codigo} en sucursal {$sucursal->nombre}\n";
                }
            }
        }

        echo "Migración de datos completada\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Esta migración no tiene down() porque modifica datos existentes
        // Si se necesita revertir, hacerlo manualmente desde un backup
        echo "Esta migración de datos no se puede revertir automáticamente\n";
        echo "Restaurar desde backup si es necesario\n";
    }
};
