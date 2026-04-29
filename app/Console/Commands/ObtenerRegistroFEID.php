<?php

namespace App\Console\Commands;

use App\Models\Business;
use Illuminate\Console\Command;

class ObtenerRegistroFEID extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:obtener-registrofe-id {--nit= : NIT específico a procesar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Obtiene el registrofe_id de la empresa y lo guarda en la base de datos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $nitOption = (string) ($this->option('nit') ?? '');
        $nitNormalized = preg_replace('/\D/', '', $nitOption);

        $this->info('Obteniendo registrofe_id de las empresas...');
        $ok = 0;
        $failed = 0;

        $businessesQuery = Business::query();
        if (!empty($nitNormalized)) {
            $businessesQuery->whereRaw("REPLACE(nit, '-', '') = ?", [$nitNormalized]);
        }

        $businesses = $businessesQuery->get();

        if ($businesses->isEmpty()) {
            if (!empty($nitNormalized)) {
                $this->warn("No se encontró un negocio con el NIT especificado: {$nitOption}");
                return Command::FAILURE;
            }

            $this->warn('No hay negocios para procesar.');
            return Command::SUCCESS;
        }

        foreach($businesses as $business){
            /** @var Business $business */
            $registroFeId = $business->ensureRegistroFeId();

            if($registroFeId){
                $this->info("Empresa {$business->nombre} actualizada con registrofe_id: {$registroFeId}");
                $ok++;
            } else {
                $this->warn("No se encontró empresa en registro_fe para NIT/DUI: {$business->formatted_nit}/{$business->formatted_dui}, Nombre: {$business->nombre}");
                $failed++;
            }
        }
        $this->info("Proceso completado. Empresas actualizadas: {$ok}, Empresas no encontradas: {$failed}");
    }
}
