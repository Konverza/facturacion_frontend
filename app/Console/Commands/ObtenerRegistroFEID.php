<?php

namespace App\Console\Commands;

use App\Models\Business;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ObtenerRegistroFEID extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:obtener-registrofe-id';

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
        $this->info('Obteniendo registrofe_id de las empresas...');
        $ok = 0;
        $failed = 0;

        $businesses = Business::all();
        foreach($businesses as $business){
            $searchValues = [
                $business->formatted_nit,
                $business->nit,
                $business->formatted_dui,
                $business->dui,
            ];

            $empresa = DB::connection('registro_fe')
                ->table('empresas')
                ->where("softDelete", 0)
                ->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(datos_empresa, "$.dui_nit")) = ?', [$business->formatted_nit])
                ->first();
            if(!$empresa){
                $empresa = DB::connection('registro_fe')
                    ->table('empresas')
                    ->where("softDelete", 0)
                    ->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(datos_empresa, "$.dui_nit")) = ?', [$business->nit])
                    ->first();
            }
            if(!$empresa){
                $empresa = DB::connection('registro_fe')
                    ->table('empresas')
                    ->where("softDelete", 0)
                    ->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(datos_empresa, "$.dui_nit")) = ?', [$business->formatted_dui])
                    ->first();
            }
            if(!$empresa){
                $empresa = DB::connection('registro_fe')
                    ->table('empresas')
                    ->where("softDelete", 0)
                    ->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(datos_empresa, "$.dui_nit")) = ?', [$business->dui])
                    ->first();
            }

            if($empresa){
                $business->registrofe_id = $empresa->id;
                $business->save();
                $this->info("Empresa {$business->nombre} actualizada con registrofe_id: {$empresa->id}");
                $ok++;
            } else {
                $this->warn("No se encontró empresa en registro_fe para NIT/DUI: {$business->formatted_nit}/{$business->formatted_dui}, Nombre: {$business->nombre}");
                $failed++;
            }
        }
        $this->info("Proceso completado. Empresas actualizadas: {$ok}, Empresas no encontradas: {$failed}");
    }
}
