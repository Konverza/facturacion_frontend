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
                            {business_id : ID del negocio a procesar}
                            {--target-stock=99999 : Stock objetivo a reconstruir (por defecto 99999)}
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
        $businessId = $this->argument('business_id');
        $targetStock = (float) $this->option('target-stock');

        $this->info('=== Reconstrucción de Stock de Productos ===');
        $this->info($dryRun ? '🔍 MODO PRUEBA (no se modificarán datos)' : '⚠️  MODO REAL (se modificarán datos)');
        $this->newLine();

        // Obtener el negocio
        $business = Business::find($businessId);

        if (!$business) {
            $this->error("No se encontró el negocio con ID: {$businessId}");
            return Command::FAILURE;
        }

        $this->info("📦 Procesando negocio: {$business->nombre} (ID: {$business->id})");
        $this->newLine();

        // Obtener stocks de sucursales con el valor objetivo (99999)
        $stocksAfectados = BranchProductStock::whereHas('businessProduct', function ($query) use ($businessId) {
            $query->where('business_id', $businessId);
        })
        ->where('stockActual', $targetStock)
        ->get();

        if ($stocksAfectados->isEmpty()) {
            $this->warn("No se encontraron productos con stock = {$targetStock} en las sucursales de este negocio.");
            return Command::SUCCESS;
        }

        $this->info("🔍 Productos encontrados con stock = {$targetStock}: {$stocksAfectados->count()}");
        $this->newLine();

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
        $this->newLine(2);

        // Mostrar detalles de la reconstrucción
        $this->info('=== Detalle de Reconstrucción ===');
        $this->table(
            ['Producto', 'Sucursal', 'Stock Anterior', 'Stock Inicial', 'Entradas', 'Salidas', 'Stock Calculado', 'Estado'],
            array_map(function ($detalle) {
                return [
                    substr($detalle['producto'], 0, 30),
                    substr($detalle['sucursal'], 0, 20),
                    $detalle['stock_anterior'],
                    $detalle['stock_inicial'],
                    $detalle['entradas'],
                    $detalle['salidas'],
                    $detalle['stock_calculado'],
                    $detalle['estado'],
                ];
            }, array_slice($detalles, 0, 20)) // Mostrar solo los primeros 20
        );

        if (count($detalles) > 20) {
            $this->info("... y " . (count($detalles) - 20) . " productos más.");
        }

        // Resumen final
        $this->newLine();
        $this->info('=== Resumen de Reconstrucción ===');
        $this->table(
            ['Métrica', 'Cantidad'],
            [
                ['Productos procesados', $stocksAfectados->count()],
                ['Productos reconstruidos', $totalReconstruidos],
                ['Errores', $totalErrores],
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
