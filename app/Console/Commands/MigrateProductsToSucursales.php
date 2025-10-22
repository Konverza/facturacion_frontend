<?php

namespace App\Console\Commands;

use App\Models\Business;
use App\Models\BusinessProduct;
use App\Models\BranchProductStock;
use App\Models\Sucursal;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MigrateProductsToSucursales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:migrate-to-sucursales 
                            {--business_id= : ID del negocio específico a migrar. Si no se proporciona, migra todos}
                            {--dry-run : Ejecutar en modo prueba sin modificar datos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migra los productos existentes del sistema actual (vinculados directamente al negocio) al nuevo sistema de inventario por sucursales';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $businessId = $this->option('business_id');

        $this->info('=== Migración de Productos a Sistema de Sucursales ===');
        $this->info($dryRun ? '🔍 MODO PRUEBA (no se modificarán datos)' : '⚠️  MODO REAL (se modificarán datos)');
        $this->newLine();

        // Obtener negocios a procesar
        $businesses = $businessId 
            ? Business::where('id', $businessId)->get()
            : Business::all();

        if ($businesses->isEmpty()) {
            $this->error('No se encontraron negocios para procesar.');
            return Command::FAILURE;
        }

        $totalProductos = 0;
        $totalMigrados = 0;
        $totalGlobales = 0;
        $totalErrores = 0;

        foreach ($businesses as $business) {
            $this->info("📦 Procesando negocio: {$business->nombre} (ID: {$business->id})");
            
            // Obtener sucursal principal (primera sucursal del negocio)
            $sucursalPrincipal = Sucursal::where('business_id', $business->id)
                ->orderBy('id')
                ->first();

            if (!$sucursalPrincipal) {
                $this->warn("  ⚠️  No se encontró ninguna sucursal para este negocio. Creando sucursal por defecto...");
                
                if (!$dryRun) {
                    $sucursalPrincipal = Sucursal::create([
                        'business_id' => $business->id,
                        'nombre' => 'Sucursal Principal',
                        'departamento' => '01',
                        'municipio' => '01',
                        'complemento' => 'Sucursal Principal',
                        'telefono' => $business->telefono ?? '0000-0000',
                        'correo' => $business->correo_responsable ?? 'principal@example.com',
                        'codSucursal' => 'M001',
                    ]);
                    $this->info("  ✅ Sucursal creada: {$sucursalPrincipal->nombre}");
                }
            } else {
                $this->info("  🏢 Usando sucursal: {$sucursalPrincipal->nombre}");
            }

            // Obtener productos del negocio
            $productos = BusinessProduct::where('business_id', $business->id)->get();
            $totalProductos += $productos->count();

            if ($productos->isEmpty()) {
                $this->warn("  ⚠️  No se encontraron productos para este negocio.");
                $this->newLine();
                continue;
            }

            $this->info("  📋 Productos encontrados: {$productos->count()}");

            $bar = $this->output->createProgressBar($productos->count());
            $bar->start();

            foreach ($productos as $producto) {
                try {
                    if (!$dryRun) {
                        DB::beginTransaction();
                    }

                    // Caso 1: Productos SIN control de stock -> marcar como globales
                    if (!$producto->has_stock && !$producto->is_global) {
                        if (!$dryRun) {
                            $producto->update(['is_global' => true]);
                        }
                        $totalGlobales++;
                    }
                    // Caso 2: Productos CON control de stock -> crear registro por sucursal
                    elseif ($producto->has_stock && !$producto->is_global) {
                        // Verificar si ya existe en la sucursal
                        $stockExistente = BranchProductStock::where('business_product_id', $producto->id)
                            ->where('sucursal_id', $sucursalPrincipal->id)
                            ->first();

                        if ($stockExistente) {
                            // Ya migrado, actualizar si es necesario
                            if ($stockExistente->stockActual != $producto->stockActual) {
                                if (!$dryRun) {
                                    $stockExistente->update([
                                        'stockActual' => $producto->stockActual,
                                        'stockMinimo' => $producto->stockMinimo,
                                        'estado_stock' => $producto->estado_stock,
                                    ]);
                                }
                            }
                        } else {
                            // Crear nuevo registro de stock por sucursal
                            if (!$dryRun) {
                                BranchProductStock::create([
                                    'business_product_id' => $producto->id,
                                    'sucursal_id' => $sucursalPrincipal->id,
                                    'stockActual' => $producto->stockActual ?? 0,
                                    'stockMinimo' => $producto->stockMinimo ?? 0,
                                    'estado_stock' => $producto->estado_stock ?? 'disponible',
                                ]);
                            }
                            $totalMigrados++;
                        }
                    }

                    if (!$dryRun) {
                        DB::commit();
                    }

                } catch (\Exception $e) {
                    if (!$dryRun) {
                        DB::rollBack();
                    }
                    $totalErrores++;
                    Log::error("Error migrando producto {$producto->id}: " . $e->getMessage());
                }

                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);
        }

        // Resumen final
        $this->newLine();
        $this->info('=== Resumen de Migración ===');
        $this->table(
            ['Métrica', 'Cantidad'],
            [
                ['Negocios procesados', $businesses->count()],
                ['Total de productos', $totalProductos],
                ['Productos migrados a sucursales', $totalMigrados],
                ['Productos marcados como globales', $totalGlobales],
                ['Errores', $totalErrores],
            ]
        );

        if ($dryRun) {
            $this->newLine();
            $this->warn('⚠️  Ejecutado en modo prueba. Para aplicar los cambios ejecuta sin --dry-run');
        } else {
            $this->newLine();
            $this->info('✅ Migración completada exitosamente.');
            $this->info('📝 Nota: Los campos stockActual, stockMinimo y estado_stock en business_product ya no se usan.');
            $this->info('   Ahora se gestionan por sucursal en business_product_stock.');
            $this->info('🌐 Los productos sin control de stock (has_stock = false) se marcaron como globales (is_global = true).');
        }

        return Command::SUCCESS;
    }
}
