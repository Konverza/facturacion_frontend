<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Business;
use App\Models\Sucursal;
use App\Models\PuntoVenta;
use App\Models\BusinessUser;
use Illuminate\Support\Facades\Http;

class SyncBusinessData extends Command
{
    protected $signature = 'sync:business-data';
    protected $description = 'Sincroniza datos de empresa desde API externa y crea sucursal y punto de venta por defecto';

    protected $octopusUrl;

    public function __construct()
    {
        parent::__construct();
        $this->octopusUrl = env('OCTOPUS_API_URL', 'https://localhost:8000');
    }

    public function handle()
    {
        $this->info('Iniciando sincronización de negocios...');

        $businesses = Business::all();

        foreach ($businesses as $business) {
            $this->info("Procesando NIT: {$business->nit}");

            try {
                $response = Http::timeout(30)->get("{$this->octopusUrl}/datos_empresa/nit/{$business->nit}");

                if (!$response->ok()) {
                    $this->error("No se pudo obtener datos para NIT: {$business->nit}");
                    continue;
                }

                $datos = $response->json();

                // Crear Sucursal si no existe
                $sucursal = Sucursal::firstOrCreate(
                    [
                        'codSucursal' => 'S001',
                        'business_id' => $business->id
                    ],
                    [
                        'nombre' => 'Casa Matriz',
                        'departamento' => $datos['departamento'],
                        'municipio' => $datos['municipio'],
                        'complemento' => $datos['complemento'],
                        'telefono' => $datos['telefono'],
                        'correo' => $datos['correo']
                    ]
                );

                // Crear Punto de Venta si no existe
                $puntoVenta = PuntoVenta::firstOrCreate(
                    [
                        'codPuntoVenta' => 'P001',
                        'sucursal_id' => $sucursal->id
                    ],
                    [
                        'nombre' => 'PdV principal'
                    ]
                );

                // Asignar default_pos_id a los usuarios del negocio
                $businessUsers = BusinessUser::where('business_id', $business->id)->get();

                foreach ($businessUsers as $user) {
                    $user->default_pos_id = $puntoVenta->id;
                    $user->save();
                }

                $this->info("✔ Se asignó el PdV principal como default a los usuarios del negocio.");

                $this->info("✔ Negocio {$business->nombre} sincronizado exitosamente.");
            } catch (\Exception $e) {
                $this->error("Error procesando NIT {$business->nit}: " . $e->getMessage());
            }
        }

        $this->info('Sincronización completada.');
    }
}
