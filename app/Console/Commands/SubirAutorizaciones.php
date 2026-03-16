<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Business;
use Illuminate\Support\Facades\Http;
use App\Services\HaciendaService;

class SubirAutorizaciones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:subir-autorizaciones {--nit= : NIT específico a procesar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subir autorizaciones en PDF del ambiente de producción a los clientes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $nitOption = (string) ($this->option('nit') ?? '');
        $nitNormalized = preg_replace('/\D/', '', $nitOption);

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

        foreach ($businesses as $business) {
            $haciendaService = new HaciendaService($business->nit, $business->dui);
            $token = $haciendaService->getAuthToken();

            if (!$token) {
                $this->error("No se pudo obtener el token de autenticación para el negocio con NIT {$business->nit}. Se omite.");
                continue;
            }

            $resoluciones = Http::withToken($token)
                ->timeout(30)
                ->get(env("HACIENDA_RESOLUCION_URL") . $business->nit)
                ->json();

            if (count($resoluciones) > 0) {
                $latestResolution = collect($resoluciones)->sortByDesc('fechaResolucion')->first();
                $resolucionId = $latestResolution['id'];
                $pdfResponse = Http::withToken($token)
                    ->timeout(30)
                    ->get(env("HACIENDA_PDF_URL") . $resolucionId);

                if ($pdfResponse->ok()) {
                    $pdfObject = $pdfResponse->json();
                    $pdfContent = base64_decode($pdfObject['pdf']);
                    $tmpPath = storage_path("app/tmp/{$business->nit}_resolucion.pdf");
                    file_put_contents($tmpPath, $pdfContent);

                    $uploadResponse = Http::withToken($registroFeApiToken)
                        ->timeout(60)
                        ->attach("archivos[path_pdf]", file_get_contents($tmpPath), basename($tmpPath))
                        ->post("{$registroFeApiUrl}/subir-archivos-s3", [
                            'id_empresa' => (string) $business->registrofe_id,
                        ]);

                    // Luego de subirlo, puedes eliminar el archivo temporal
                    if (file_exists($tmpPath)) {
                        unlink($tmpPath);
                    }

                    if ($uploadResponse->ok()) {
                        $this->info("Autorización para negocio con NIT: {$business->nit} subida exitosamente.");
                    } else {
                        $this->error("Error al subir Autorización para negocio con NIT: {$business->nit}. Respuesta: {$uploadResponse->body()}");
                    }

                } else {
                    $this->error("Error al obtener el PDF de la resolución para el negocio con NIT {$business->nit}. Respuesta: {$pdfResponse->body()}");
                    continue;
                }
            } else {
                $this->warn("No se encontraron resoluciones para el negocio con NIT {$business->nit} en Hacienda. Se omite.");
                continue;
            }
        }
    }
}
