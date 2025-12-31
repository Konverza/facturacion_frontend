<?php

namespace App\Console\Commands;

use App\Models\Business;
use App\Models\BusinessProduct;
use App\Models\BranchProductStock;
use App\Models\BusinessProductMovement;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RebuildProductStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:rebuild-stock 
                            {--business_id= : ID del negocio específico a procesar. Si no se proporciona, procesa todos}
                            {--target-stock=99999 : Stock mínimo a reconstruir - procesa productos con este valor o mayor (por defecto 99999)}
                            {--dry-run : Ejecutar en modo prueba sin modificar datos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reconstruye el stock de productos en sucursales a partir del stock inicial y los movimientos de entrada/salida';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $businessId = $this->option('business_id');
        $targetStock = (float) $this->option('target-stock');

        $this->info('=== Reconstrucción de Stock de Productos ===');
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

        $this->info($businessId 
            ? "📦 Procesando negocio específico (ID: {$businessId})"
            : "🌐 Procesando TODOS los negocios ({$businesses->count()} en total)"
        );
        $this->newLine();

        $totalReconstruidosGlobal = 0;
        $totalErroresGlobal = 0;
        $totalProductosAfectadosGlobal = 0;

        foreach ($businesses as $business) {
            $this->info("📦 Negocio: {$business->nombre} (ID: {$business->id})");

            // Obtener stocks de sucursales con el valor objetivo o mayor
            $stocksAfectados = BranchProductStock::whereHas('businessProduct', function ($query) use ($business) {
                $query->where('business_id', $business->id);
            })
            ->where('stockActual', '>=', $targetStock)
            ->get();

            if ($stocksAfectados->isEmpty()) {
                $this->comment("  ℹ️  No se encontraron productos con stock >= {$targetStock}");
                $this->newLine();
                continue;
            }

            $this->info("  🔍 Productos encontrados: {$stocksAfectados->count()}");
            $totalProductosAfectadosGlobal += $stocksAfectados->count();

            $totalReconstruidos = 0;
            $totalErrores = 0;
            $detalles = [];

            $bar = $this->output->createProgressBar($stocksAfectados->count());
            $bar->start();

            foreach ($stocksAfectados as $stock) {
                try {
                    if (!$dryRun) {
                        DB::beginTransaction();
                    }

                    $producto = $stock->businessProduct;
                    
                    // Obtener stock inicial del producto
                    $stockInicial = $producto->stockInicial ?? 0;

                    // Obtener movimientos de este producto
                    $movimientos = BusinessProductMovement::where('business_product_id', $producto->id)
                        ->orderBy('created_at', 'asc')
                        ->get();

                    // Calcular stock real: inicial + entradas - salidas
                    $entradas = $movimientos->where('tipo', 'entrada')->sum('cantidad');
                    $salidas = $movimientos->where('tipo', 'salida')->sum('cantidad');
                    
                    $stockCalculado = $stockInicial + $entradas - $salidas;

                    // Asegurar que no sea negativo
                    $stockCalculado = max(0, $stockCalculado);

                    // Determinar estado del stock
                    $estadoStock = 'disponible';
                    if ($stockCalculado <= 0) {
                        $estadoStock = 'agotado';
                    } elseif ($stockCalculado <= ($stock->stockMinimo ?? 0)) {
                        $estadoStock = 'por_agotarse';
                    }

                    // Registrar detalle para el resumen
                    $detalles[] = [
                        'producto' => $producto->descripcion,
                        'sucursal' => $stock->sucursal->nombre ?? 'N/A',
                        'stock_anterior' => $targetStock,
                        'stock_inicial' => $stockInicial,
                        'entradas' => $entradas,
                        'salidas' => $salidas,
                        'stock_calculado' => $stockCalculado,
                        'estado' => $estadoStock,
                    ];

                    // Actualizar el stock
                    if (!$dryRun) {
                        $stock->update([
                            'stockActual' => $stockCalculado,
                            'estado_stock' => $estadoStock,
                        ]);
                        DB::commit();
                    }

                    $totalReconstruidos++;

                } catch (\Exception $e) {
                    if (!$dryRun) {
                        DB::rollBack();
                    }
                    $totalErrores++;
                    Log::error("Error reconstruyendo stock para producto {$producto->id}: " . $e->getMessage());
                    
                    $detalles[] = [
                        'producto' => $producto->descripcion ?? 'Desconocido',
                        'sucursal' => 'ERROR',
                        'stock_anterior' => $targetStock,
                        'stock_inicial' => '-',
                        'entradas' => '-',
                        'salidas' => '-',
                        'stock_calculado' => 'ERROR',
                        'estado' => $e->getMessage(),
                    ];
                }

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();

            // Mostrar resumen del negocio actual
            $this->info("  ✅ Reconstruidos: {$totalReconstruidos} | ❌ Errores: {$totalErrores}");
            $this->newLine();

            $totalReconstruidosGlobal += $totalReconstruidos;
            $totalErroresGlobal += $totalErrores;
        }

        // Resumen final global
        $this->newLine();
        $this->info('=== Resumen Global de Reconstrucción ===');
        $this->table(
            ['Métrica', 'Cantidad'],
            [
                ['Negocios procesados', $businesses->count()],
                ['Total productos afectados', $totalProductosAfectadosGlobal],
                ['Total productos reconstruidos', $totalReconstruidosGlobal],
                ['Total errores', $totalErroresGlobal],
            ]
        );

        if ($dryRun) {
            $this->newLine();
            $this->warn('⚠️  Ejecutado en modo prueba. Para aplicar los cambios ejecuta sin --dry-run');
        } else {
            $this->newLine();
            $this->info('✅ Reconstrucción completada exitosamente.');
            $this->info('📝 El stock se calculó como: Stock Inicial + Entradas - Salidas');
        }

        return Command::SUCCESS;
    }
}
