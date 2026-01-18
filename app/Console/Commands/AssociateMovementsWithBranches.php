<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BusinessProductMovement;
use App\Models\Business;
use App\Models\Sucursal;
use App\Models\PuntoVenta;
use Illuminate\Support\Facades\Http;

class AssociateMovementsWithBranches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'movements:associate-branches {--business_id= : ID del business específico}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asocia sucursales y puntos de venta a los movimientos de productos basándose en los DTEs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $businessId = $this->option('business_id');
        
        $businesses = $businessId 
            ? Business::where('id', $businessId)->get()
            : Business::all();

        if ($businesses->isEmpty()) {
            $this->error('No se encontraron businesses para procesar.');
            return 1;
        }

        $totalProcessed = 0;
        $totalUpdated = 0;

        foreach ($businesses as $business) {
            $this->info("Procesando business: {$business->nombre_de_la_empresa} (ID: {$business->id})");

            // Obtener movimientos sin sucursal o punto de venta asociado
            $movements = BusinessProductMovement::whereHas('businessProduct', function ($query) use ($business) {
                    $query->where('business_id', $business->id);
                })
                ->where(function($q) {
                    $q->whereNull('sucursal_id')->orWhereNull('punto_venta_id');
                })
                ->whereNotNull('numero_factura')
                ->get();

            if ($movements->isEmpty()) {
                $this->line("  No hay movimientos para procesar en este business.");
                continue;
            }

            // Obtener DTEs del business
            try {
                $response = Http::get(env("OCTOPUS_API_URL") . '/dtes/?nit=' . $business->nit);
                $dtes = $response->json()["items"] ?? [];
            } catch (\Exception $e) {
                $this->error("  Error al obtener DTEs: {$e->getMessage()}");
                continue;
            }

            $dteByCodGeneracion = [];
            foreach ($dtes as $dte) {
                if (isset($dte["codGeneracion"])) {
                    $dteByCodGeneracion[$dte["codGeneracion"]] = $dte;
                }
            }

            $bar = $this->output->createProgressBar($movements->count());
            $bar->start();

            foreach ($movements as $movement) {
                $totalProcessed++;
                $updated = false;

                if (isset($dteByCodGeneracion[$movement->numero_factura])) {
                    $dte = $dteByCodGeneracion[$movement->numero_factura];

                    // Asociar sucursal si no existe
                    if (!$movement->sucursal_id && isset($dte['codSucursal'])) {
                        $sucursal = Sucursal::where('codSucursal', $dte['codSucursal'])
                            ->where('business_id', $business->id)
                            ->first();
                        
                        if ($sucursal) {
                            $movement->sucursal_id = $sucursal->id;
                            $updated = true;
                        }
                    }

                    // Asociar punto de venta si no existe
                    if (!$movement->punto_venta_id && isset($dte['codPuntoVenta'])) {
                        $puntoVenta = PuntoVenta::where('codPuntoVenta', $dte['codPuntoVenta'])
                            ->whereHas('sucursal', function($q) use ($business) {
                                $q->where('business_id', $business->id);
                            })
                            ->first();
                        
                        if ($puntoVenta) {
                            $movement->punto_venta_id = $puntoVenta->id;
                            $updated = true;
                        }
                    }

                    if ($updated) {
                        $movement->save();
                        $totalUpdated++;
                    }
                }

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
        }

        $this->newLine();
        $this->info("✅ Proceso completado");
        $this->line("  Movimientos procesados: {$totalProcessed}");
        $this->line("  Movimientos actualizados: {$totalUpdated}");

        return 0;
    }
}
