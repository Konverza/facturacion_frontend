<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Business;
use Illuminate\Support\Facades\Http;

class SubirClaves extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:subir-claves {--nit= : NIT específico a procesar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sube las claves de la tabla "auth" a la base de RegistroFE';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $nitOption = (string) ($this->option('nit') ?? '');
        $nitNormalized = preg_replace('/\D/', '', $nitOption);

        $registroFeApiUrl = rtrim((string) env('REGISTRO_FE_API_URL'), '/');
        $loginResponse = Http::timeout(30)->post("{$registroFeApiUrl}/login", [
            'email' => env('REGISTRO_FE_API_USER'),
            'password' => env('REGISTRO_FE_API_PASS'),
        ]);

        $registroFeApiToken = $loginResponse->json()['access_token'] ?? null;

        if (!$loginResponse->ok() || !$registroFeApiToken) {
            $this->error('No se pudo autenticar con Registro FE para subir certificados.');
            return Command::FAILURE;
        }

        $username = env("PRUEBAS_USER");
        $password = env("PRUEBAS_PASS");
        $token_response = Http::asForm()->post(env("PRUEBAS_URL") . "/auth/login", [
            "username" => $username,
            "password" => $password
        ]);
        $token = $token_response->json()['access_token'] ?? null;
        if (!$token_response->ok() || !$token) {
            $this->error('No se pudo autenticar con el sistema de pruebas para obtener las claves.');
            return Command::FAILURE;
        }

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

        foreach ($businesses as $business) {
            if (empty($business->registrofe_id)) {
                $this->warn("Negocio con NIT {$business->nit} no tiene registrofe_id. Se omite.");
                continue;
            }

            $credentials_response = Http::withToken($token)
                ->get(env("PRUEBAS_URL") . "/credenciales/nit/" . $business->nit);
            $credentials = $credentials_response->json();

            if (!$credentials_response->ok() || empty($credentials)) {
                $this->warn("No se pudieron obtener las claves para el negocio con NIT {$business->nit}.");
                continue;
            }

            $uploadResponse = Http::withToken($registroFeApiToken)
                ->timeout(60)
                ->post("{$registroFeApiUrl}/guardar-claves", [
                    'empresa_id' => (string) $business->registrofe_id,
                    'claves' => [
                        'clave_API' => $credentials['api_password'],
                        'clave_PRI' => $credentials['certificate_password']
                    ]
                ]);

            if ($uploadResponse->ok()) {
                $this->info("Claves subidas exitosamente para el negocio con NIT {$business->nit}.");
            } else {
                $this->error("Error al subir las claves para el negocio con NIT {$business->nit}. Respuesta: {$uploadResponse->body()}");
            }
        }
    }
}
