<?php

namespace App\Console\Commands;

use App\Models\BusinessProduct;
use App\Models\BranchProductStock;
use App\Models\Sucursal;
use Illuminate\Console\Command;

class DiagnoseProductAvailability extends Command
{
    protected $signature = 'products:diagnose {--business_id=1} {--sucursal_id=1}';
    protected $description = 'Diagnostica por qué algunos productos no aparecen en DteProduct';

    public function handle()
    {
        $businessId = $this->option('business_id');
        $sucursalId = $this->option('sucursal_id');

        $this->info("=== Diagnóstico de Productos ===");
        $this->info("Business ID: {$businessId}");
        $this->info("Sucursal ID: {$sucursalId}");
        $this->newLine();

        // Total de productos
        $total = BusinessProduct::where('business_id', $businessId)->count();
        $this->info("📦 Total productos del negocio: {$total}");
        $this->newLine();

        // Productos globales
        $globales = BusinessProduct::where('business_id', $businessId)
            ->where('is_global', true)
            ->count();
        $this->info("🌐 Productos globales (is_global=true): {$globales}");

        // Productos sin control de stock
        $sinStock = BusinessProduct::where('business_id', $businessId)
            ->where('has_stock', false)
            ->count();
        $this->info("📋 Productos sin control de stock (has_stock=false): {$sinStock}");

        // Productos con control de stock
        $conStock = BusinessProduct::where('business_id', $businessId)
            ->where('has_stock', true)
            ->count();
        $this->info("📊 Productos con control de stock (has_stock=true): {$conStock}");
        $this->newLine();

        // Stock en sucursal
        $this->info("=== Stock en Sucursal {$sucursalId} ===");
        $enSucursal = BranchProductStock::where('sucursal_id', $sucursalId)->count();
        $this->info("📦 Registros en branch_product_stock: {$enSucursal}");

        $disponibles = BranchProductStock::where('sucursal_id', $sucursalId)
            ->whereIn('estado_stock', ['disponible', 'por_agotarse'])
            ->count();
        $this->info("✅ Con stock disponible/por_agotarse: {$disponibles}");

        $agotados = BranchProductStock::where('sucursal_id', $sucursalId)
            ->where('estado_stock', 'agotado')
            ->count();
        $this->info("❌ Con stock agotado: {$agotados}");
        $this->newLine();

        // Simular query de DteProduct
        $this->info("=== Simulación de Query DteProduct ===");
        $query = BusinessProduct::where('business_id', $businessId)
            ->availableInBranch($sucursalId);
        
        $disponiblesEnVista = $query->count();
        $this->info("👁️  Productos que deberían aparecer en vista: {$disponiblesEnVista}");
        $this->newLine();

        // Desglose
        $this->info("=== Desglose de Productos Disponibles ===");
        $productos = $query->get();
        
        $globalesEncontrados = $productos->where('is_global', true)->count();
        $conStockEncontrados = $productos->where('has_stock', true)->where('is_global', false)->count();
        $sinStockEncontrados = $productos->where('has_stock', false)->where('is_global', false)->count();

        $this->table(
            ['Tipo', 'Cantidad'],
            [
                ['Globales', $globalesEncontrados],
                ['Con stock en sucursal', $conStockEncontrados],
                ['Sin stock (no globales)', $sinStockEncontrados],
                ['TOTAL', $disponiblesEnVista],
            ]
        );

        // Productos con stock pero sin registro en sucursal
        $this->newLine();
        $this->info("=== Problemas Detectados ===");
        
        $conStockSinRegistro = BusinessProduct::where('business_id', $businessId)
            ->where('has_stock', true)
            ->where('is_global', false)
            ->whereDoesntHave('branchStocks', function ($q) use ($sucursalId) {
                $q->where('sucursal_id', $sucursalId);
            })
            ->count();

        if ($conStockSinRegistro > 0) {
            $this->warn("⚠️  {$conStockSinRegistro} productos con control de stock NO tienen registro en branch_product_stock para esta sucursal");
            $this->info("   Solución: Ejecutar 'php artisan products:migrate-to-sucursales'");
        } else {
            $this->info("✅ Todos los productos con stock tienen registro en la sucursal");
        }

        return Command::SUCCESS;
    }
}
