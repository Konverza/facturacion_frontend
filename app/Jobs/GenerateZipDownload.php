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

            $desde = $this->zipDownloadJob->fecha_inicio->format('Y-m-d') . 'T00:00:00';
            $hasta = $this->zipDownloadJob->fecha_fin->format('Y-m-d') . 'T23:59:59';

            $response = Http::get(env("OCTOPUS_API_URL") . "/dtes/", [
                'nit' => $this->business->nit,
                'fechaInicio' => $desde,
                'fechaFin' => $hasta,
            ]);

            if (!$response->successful()) {
                throw new \Exception('Error al obtener DTEs desde la API');
            }

            $dtes = $response->json()["items"] ?? [];
            $dtesProcessable = array_filter($dtes, fn($dte) => $dte["estado"] == "PROCESADO");
            $totalDtes = count($dtesProcessable);

            $this->zipDownloadJob->update(['total_dtes' => $totalDtes]);

            if ($totalDtes === 0) {
                throw new \Exception('No se encontraron DTEs procesados en el rango de fechas');
            }

            $fechaInicioFormateada = $this->zipDownloadJob->fecha_inicio->format('dmY');
            $fechaFinFormateada = $this->zipDownloadJob->fecha_fin->format('dmY');
            $zipFileName = "dtes_{$fechaInicioFormateada}_{$fechaFinFormateada}.zip";

            $s3Path = "zip_downloads/{$this->business->id}/{$zipFileName}";

            $tempZipPath = sys_get_temp_dir() . '/' . uniqid('zip_') . '.zip';

            $zip = new ZipArchive();
            if ($zip->open($tempZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
                throw new \Exception('No se pudo crear el archivo ZIP temporal');
            }
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
