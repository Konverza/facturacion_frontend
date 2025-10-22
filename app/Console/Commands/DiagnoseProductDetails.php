<?php

namespace App\Console\Commands;

use App\Models\BusinessProduct;
use App\Models\BranchProductStock;
use Illuminate\Console\Command;

class DiagnoseProductDetails extends Command
{
    protected $signature = 'products:diagnose-details {--business_id=1} {--sucursal_id=1}';
    protected $description = 'Muestra detalles de cada producto y por qué aparece o no en la vista';

    public function handle()
    {
        $businessId = $this->option('business_id');
        $sucursalId = $this->option('sucursal_id');

        $this->info("=== Diagnóstico Detallado de Productos ===");
        $this->info("Business ID: {$businessId}");
        $this->info("Sucursal ID: {$sucursalId}");
        $this->newLine();

        // Obtener TODOS los productos del negocio
        $todosLosProductos = BusinessProduct::where('business_id', $businessId)
            ->orderBy('id')
            ->get();

        $this->info("📦 Total de productos en business_product: {$todosLosProductos->count()}");
        $this->newLine();

        // Obtener productos que SÍ aparecen según el scope
        $productosDisponibles = BusinessProduct::where('business_id', $businessId)
            ->availableInBranch($sucursalId)
            ->pluck('id')
            ->toArray();

        $this->info("✅ Productos que aparecen en la vista (según availableInBranch): " . count($productosDisponibles));
        $this->newLine();

        // Analizar cada producto
        $this->info("=== Análisis Detallado por Producto ===");
        $this->newLine();

        $disponibles = 0;
        $noDisponibles = 0;

        foreach ($todosLosProductos as $producto) {
            $aparece = in_array($producto->id, $productosDisponibles);
            $stock = $producto->getStockForBranch($sucursalId);

            if (!$aparece) {
                $this->warn("❌ ID: {$producto->id} | Código: {$producto->codigo} | {$producto->descripcion}");
                $this->line("   - is_global: " . ($producto->is_global ? 'true' : 'false'));
                $this->line("   - has_stock: " . ($producto->has_stock ? 'true' : 'false'));
                
                if ($stock) {
                    $this->line("   - Stock en sucursal: {$stock->stockActual}");
                    $this->line("   - Estado stock: {$stock->estado_stock}");
                    
                    if ($stock->estado_stock === 'agotado') {
                        $this->error("   ⚠️  PROBLEMA: El producto está AGOTADO (estado_stock='agotado')");
                        $this->info("   💡 Solución: Actualizar el stock o cambiar estado_stock a 'disponible'");
                    }
                } else {
                    if ($producto->has_stock && !$producto->is_global) {
                        $this->error("   ⚠️  PROBLEMA: Producto con control de stock pero SIN registro en branch_product_stock");
                        $this->info("   💡 Solución: Crear registro en branch_product_stock o marcar como global");
                    }
                }
                $this->newLine();
                $noDisponibles++;
            } else {
                $disponibles++;
            }
        }

        // Resumen
        $this->newLine();
        $this->info("=== Resumen ===");
        $this->table(
            ['Estado', 'Cantidad'],
            [
                ['Total productos', $todosLosProductos->count()],
                ['Disponibles en vista', $disponibles],
                ['NO disponibles', $noDisponibles],
            ]
        );

        // Recomendaciones
        if ($noDisponibles > 0) {
            $this->newLine();
            $this->warn("=== Recomendaciones ===");
            
            $agotados = BranchProductStock::where('sucursal_id', $sucursalId)
                ->where('estado_stock', 'agotado')
                ->count();
            
            if ($agotados > 0) {
                $this->line("1. Tienes {$agotados} productos agotados. Para que aparezcan:");
                $this->line("   - Aumenta el stock: UPDATE business_product_stock SET stockActual = X WHERE ...");
                $this->line("   - O modifica el scope availableInBranch para incluir productos agotados");
            }

            $sinRegistro = BusinessProduct::where('business_id', $businessId)
                ->where('has_stock', true)
                ->where('is_global', false)
                ->whereDoesntHave('branchStocks', function ($q) use ($sucursalId) {
                    $q->where('sucursal_id', $sucursalId);
                })
                ->count();

            if ($sinRegistro > 0) {
                $this->line("2. Tienes {$sinRegistro} productos sin registro en la sucursal.");
                $this->line("   Ejecuta: php artisan products:migrate-to-sucursales --business_id={$businessId}");
            }
        }

        return Command::SUCCESS;
    }
}
