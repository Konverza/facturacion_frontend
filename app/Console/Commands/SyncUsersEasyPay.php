<?php

namespace App\Console\Commands;

use App\Models\Business;
use App\Models\BusinessUser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;


class SyncUsersEasyPay extends Command
{
    protected $octopusUrl;

    public function __construct()
    {
        parent::__construct();
        $this->octopusUrl = env('OCTOPUS_API_URL', 'http://localhost:8000');
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:users-easypay';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza los usuarios del sistema con los de EasyPay, garantizando el mismo password y acceso a la misma empresa';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Aquí va la lógica para sincronizar los usuarios con EasyPay
        $this->info('Sincronización de usuarios con EasyPay iniciada.');

        $businesses = Business::with("users.user")->get();

        foreach ($businesses as $business) {

            // Obtener NRC de API de Octopus
            $response = Http::timeout(30)->get("{$this->octopusUrl}/datos_empresa/nit/{$business->nit}");

            if (!$response->ok()) {
                $this->error("No se pudo obtener datos para NIT: {$business->nit}");
                continue;
            }
            $datos = $response->json();

            // Obtener ID de empresa en Registro Backend
            $nrc = isset($datos['nrc']) ? str_replace('-', '', $datos['nrc']) : null;
            if (!$nrc) {
                $this->error("No se encontró NRC para NIT: {$business->nit}");
                continue;
            }

            $empresa = DB::connection("registro_fe")
                ->table("empresas")
                ->whereRaw('REPLACE(JSON_UNQUOTE(JSON_EXTRACT(`datos_empresa`, "$.nrc")), "-", "") = ?', [$nrc])
                ->first();

            // Obtener el usuario más antiguo asociado al negocio
            $oldestUserEntry = $business->users->sortBy('created_at')->first();

            if ($oldestUserEntry) {
                $user = $oldestUserEntry->user;
                $this->info("Sincronizando usuario más antiguo: {$user->email} en la empresa: {$business->nombre}");
                if (!$empresa) {
                    $this->error("No se encontró empresa en Registro Backend para NRC: {$nrc}");
                    continue;
                }
                // Verificar si el usuario ya existe en la empresa
                $usuario = DB::connection("easypay")
                    ->table("users")
                    ->where('email', $user->email)
                    ->orWhere('empresa_id', $empresa->id)
                    ->first();

                if ($usuario) {
                    $this->info("Usuario ya creado: {$usuario->email}");
                } else {
                    $this->info("Usuario no encontrado, creando: {$user->email}");

                    // Obtener el correlativo más alto de usuarios
                    $correlativo = DB::connection("easypay")->table("users")->max("correlativo") + 1;

                    DB::connection("easypay")->table("users")->insert([
                        'dui' => str_replace("-", "", json_decode($empresa->datos_empresa)->dui_nit) ?? "",
                        'empresa_id' => $empresa->id,
                        'correlativo' => $correlativo,
                        'tipo' => 'user',
                        'telefono' => "+503" . str_replace("-", "", json_decode($empresa->representante_legal)->telefono ?? ""),
                        'name' => $user->name,
                        'apellidos' => "",
                        'email' => $user->email,
                        'enlace_foto' => null,
                        'password' => $user->password,
                    ]);

                    $this->info("Usuario creado: {$user->email}");
                }

                $user->update([
                    'easypay_access' => true,
                ]);

            } else {
                $this->info("No hay usuarios asociados al negocio: {$business->nombre}");
            }
        }

        $this->info('Sincronización de usuarios con EasyPay completada.');
    }
}
