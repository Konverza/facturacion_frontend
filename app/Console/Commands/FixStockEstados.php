<?php

namespace App\Console\Commands;

use App\Models\BranchProductStock;
use Illuminate\Console\Command;

class FixStockEstados extends Command
{
    protected $signature = 'products:fix-stock-estados {--business_id=} {--sucursal_id=}';
    protected $description = 'Actualiza el estado_stock de todos los productos según su stockActual';

    public function handle()
    {
        $businessId = $this->option('business_id');
        $sucursalId = $this->option('sucursal_id');

        $query = BranchProductStock::query();

        if ($sucursalId) {
            $query->where('sucursal_id', $sucursalId);
        }

        if ($businessId) {
            $query->whereHas('businessProduct', function ($q) use ($businessId) {
                $q->where('business_id', $businessId);
            });
        }

        $stocks = $query->get();

        $this->info("Actualizando estados de {$stocks->count()} registros...");
        $this->newLine();

        $updated = 0;
        $bar = $this->output->createProgressBar($stocks->count());
        $bar->start();

        foreach ($stocks as $stock) {
            $estadoAnterior = $stock->estado_stock;
            $stock->updateStockEstado();
            
            if ($stock->estado_stock !== $estadoAnterior) {
                $updated++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Proceso completado");
        $this->info("📊 Registros actualizados: {$updated}");

        return Command::SUCCESS;
    }
}
