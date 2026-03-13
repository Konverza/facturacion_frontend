<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Business;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class SubirCRT extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:subir-crt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cargar certificados de firma electrónica en Registro FE';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $baseUrl = rtrim((string) env('REGISTRO_FE_API_URL'), '/');
        $loginResponse = Http::timeout(30)->post("{$baseUrl}/login", [
            'email' => env('REGISTRO_FE_API_USER'),
            'password' => env('REGISTRO_FE_API_PASS'),
        ]);

        $token = $loginResponse->json()['access_token'] ?? null;

        if (!$loginResponse->ok() || !$token) {
            $this->error('No se pudo autenticar con Registro FE para subir certificados.');
            return Command::FAILURE;
        }

        $businesses = Business::all();

        foreach ($businesses as $business) {
            if (empty($business->registrofe_id)) {
                $this->warn("Negocio con NIT {$business->nit} no tiene registrofe_id. Se omite.");
                continue;
            }

            // Download file from s3_cert to tmp and then upload to Registro FE
            $crt_path = "{$business->nit}.crt";

            if (Storage::disk('cert_s3')->exists($crt_path)) {
                $crt_content = Storage::disk('cert_s3')->get($crt_path);
                $tmp_path = storage_path("app/tmp/{$business->nit}.crt");

                if (!is_dir(dirname($tmp_path))) {
                    mkdir(dirname($tmp_path), 0755, true);
                }

                file_put_contents($tmp_path, $crt_content);

                $file_key = (env("APP_ENV") == "local") ? "archivos[path_pruebas]" : "archivos[path_producc]";

                $uploadResponse = Http::withToken($token)
                    ->timeout(60)
                    ->attach($file_key, file_get_contents($tmp_path), basename($tmp_path))
                    ->post("{$baseUrl}/subir-archivos-s3", [
                        'id_empresa' => (string) $business->registrofe_id,
                    ]);

                // Luego de subirlo, puedes eliminar el archivo temporal
                if (file_exists($tmp_path)) {
                    unlink($tmp_path);
                }

                if ($uploadResponse->ok()) {
                    $this->info("Certificado para negocio con NIT: {$business->nit} subido exitosamente.");
                } else {
                    $this->error("Error al subir certificado para negocio con NIT: {$business->nit}. Respuesta: {$uploadResponse->body()}");
                }
            } else {
                $this->error("No se encontró el certificado para el negocio con NIT: {$business->nit}");
            }
        }

        return Command::SUCCESS;
    }
}
