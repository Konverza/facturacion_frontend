<?php

namespace App\Jobs;

use App\Models\Business;
use App\Models\ZipDownloadJob;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class GenerateZipDownload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 1;

    protected $zipDownloadJob;
    protected $business;

    /**
     * Create a new job instance.
     */
    public function __construct(ZipDownloadJob $zipDownloadJob)
    {
        $this->zipDownloadJob = $zipDownloadJob;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->zipDownloadJob->update([
                'status' => 'processing',
                'started_at' => now(),
            ]);

            $this->business = Business::find($this->zipDownloadJob->business_id);

            if (!$this->business) {
                throw new \Exception('Empresa no encontrada');
            }

            // Construir parámetros de consulta con todos los filtros
            $params = [
                'nit' => $this->business->nit,
                'emisionInicio' => $this->zipDownloadJob->fecha_inicio ? $this->zipDownloadJob->fecha_inicio->format('Y-m-d') . 'T00:00:00' : null,
                'emisionFin' => $this->zipDownloadJob->fecha_fin ? $this->zipDownloadJob->fecha_fin->format('Y-m-d') . 'T23:59:59' : null,
            ];

            // Agregar filtros opcionales si están presentes
            if ($this->zipDownloadJob->procesamiento_inicio) {
                $params['fechaInicio'] = $this->zipDownloadJob->procesamiento_inicio->format('Y-m-d') . 'T00:00:00';
            }
            if ($this->zipDownloadJob->procesamiento_fin) {
                $params['fechaFin'] = $this->zipDownloadJob->procesamiento_fin->format('Y-m-d') . 'T23:59:59';
            }
            if ($this->zipDownloadJob->cod_sucursal) {
                $params['codSucursal'] = $this->zipDownloadJob->cod_sucursal;
            }
            if ($this->zipDownloadJob->cod_punto_venta) {
                $params['codPuntoVenta'] = $this->zipDownloadJob->cod_punto_venta;
            }
            if ($this->zipDownloadJob->tipo_dte) {
                $params['tipo_dte'] = $this->zipDownloadJob->tipo_dte;
            }
            if ($this->zipDownloadJob->estado) {
                $params['estado'] = $this->zipDownloadJob->estado;
            }
            if ($this->zipDownloadJob->documento_receptor) {
                $params['documento_receptor'] = $this->zipDownloadJob->documento_receptor;
            }
            if ($this->zipDownloadJob->busqueda) {
                $params['q'] = $this->zipDownloadJob->busqueda;
            }

            $response = Http::get(env("OCTOPUS_API_URL") . "/dtes/", $params);

            if (!$response->successful()) {
                throw new \Exception('Error al obtener DTEs desde la API');
            }

            $dtes = $response->json()["items"] ?? [];

            // Si no se filtró por estado en la consulta, filtrar por PROCESADO aquí
            if (!$this->zipDownloadJob->estado) {
                $dtesProcessable = array_filter($dtes, fn($dte) => $dte["estado"] == "PROCESADO");
            } else {
                $dtesProcessable = $dtes;
            }

            $totalDtes = count($dtesProcessable);

            $this->zipDownloadJob->update(['total_dtes' => $totalDtes]);

            if ($totalDtes === 0) {
                throw new \Exception('No se encontraron DTEs con los filtros aplicados');
            }

            // Crear nombre del archivo (agregar indicador de filtros si existen)
            $fechaInicioFormateada = $this->zipDownloadJob->fecha_inicio->format('dmY');
            $fechaFinFormateada = $this->zipDownloadJob->fecha_fin->format('dmY');
            $hasFilters = $this->zipDownloadJob->tipo_dte || $this->zipDownloadJob->estado ||
                $this->zipDownloadJob->cod_sucursal || $this->zipDownloadJob->documento_receptor;
            $zipFileName = "dtes_{$fechaInicioFormateada}_{$fechaFinFormateada}" .
                ($hasFilters ? "_filtrado" : "") . ".zip";

            $s3Path = "zip_downloads/{$this->business->id}/{$zipFileName}";

            $tempZipPath = sys_get_temp_dir() . '/' . uniqid('zip_') . '.zip';

            $zip = new ZipArchive();
            if ($zip->open($tempZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
                throw new \Exception('No se pudo crear el archivo ZIP temporal');
            }

            // Crear archivo de filtros aplicados
            $filtrosTexto = "FILTROS APLICADOS EN ESTA DESCARGA\n";
            $filtrosTexto .= "=====================================\n\n";
            $filtrosTexto .= $this->zipDownloadJob->getFiltersDescription();
            $filtrosTexto .= "\n\nTotal de DTEs: {$totalDtes}\n";
            $filtrosTexto .= "Generado: " . now()->format('d/m/Y H:i:s') . "\n";

            $zip->addFromString('filtros aplicados.txt', $filtrosTexto);
            $zip->close();

            $this->zipDownloadJob->update([
                'file_path' => $s3Path,
                'file_name' => $zipFileName,
            ]);

            $jobs = [];
            foreach ($dtesProcessable as $dte) {
                $jobs[] = new ProcessSingleDteForZip(
                    $this->zipDownloadJob->id,
                    $dte,
                    $tempZipPath
                );
            }

            $zipJobId = $this->zipDownloadJob->id;
            $tempPath = $tempZipPath;

            $batch = Bus::batch($jobs)
                ->name("ZIP Download #{$zipJobId}")
                ->allowFailures()
                ->catch(function (Batch $batch, \Throwable $e) use ($zipJobId, $tempPath) {
                    Log::error("Batch fallido para ZIP ID {$zipJobId}: " . $e->getMessage());
                    if (file_exists($tempPath)) {
                        unlink($tempPath);
                    }
                })
                ->dispatch();

            Log::info("Batch creado con {$totalDtes} jobs para ZIP ID {$zipJobId}");

        } catch (\Exception $e) {
            Log::error("Error generando ZIP: " . $e->getMessage());

            $this->zipDownloadJob->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Job fallido para ZIP ID {$this->zipDownloadJob->id}: " . $exception->getMessage());

        $this->zipDownloadJob->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
            'completed_at' => now(),
        ]);
    }
}
