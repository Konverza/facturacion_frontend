<?php

namespace App\Jobs;

use App\Models\Business;
use App\Models\ReceivedZipDownloadJob;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class GenerateReceivedZipDownload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 1;

    protected $zipDownloadJob;
    protected $business;

    public function __construct(ReceivedZipDownloadJob $zipDownloadJob)
    {
        $this->zipDownloadJob = $zipDownloadJob;
    }

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

            $fechaInicio = $this->zipDownloadJob->fecha_inicio ? \Carbon\Carbon::parse($this->zipDownloadJob->fecha_inicio) : null;
            $fechaFin = $this->zipDownloadJob->fecha_fin ? \Carbon\Carbon::parse($this->zipDownloadJob->fecha_fin) : null;

            $params = [
                'nit' => $this->business->nit,
                'fechaInicio' => $fechaInicio ? $fechaInicio->format('Y-m-d') . 'T00:00:00' : null,
                'fechaFin' => $fechaFin ? $fechaFin->format('Y-m-d') . 'T23:59:59' : null,
            ];

            if ($this->zipDownloadJob->tipo_dte) {
                $params['tipo_dte'] = $this->zipDownloadJob->tipo_dte;
            }
            if ($this->zipDownloadJob->documento_emisor) {
                $params['documento_emisor'] = $this->zipDownloadJob->documento_emisor;
            }
            if ($this->zipDownloadJob->busqueda) {
                $params['q'] = $this->zipDownloadJob->busqueda;
            }

            $response = Http::get(env("OCTOPUS_API_URL") . "/dtes_recibidos/", $params);

            if (!$response->successful()) {
                throw new \Exception('Error al obtener DTEs recibidos desde la API');
            }

            $dtes = $response->json()['items'] ?? [];
            $totalDtes = count($dtes);

            $this->zipDownloadJob->update(['total_dtes' => $totalDtes]);

            if ($totalDtes === 0) {
                throw new \Exception('No se encontraron DTEs con los filtros aplicados');
            }

            $fechaInicioFormateada = $fechaInicio ? $fechaInicio->format('dmY') : now()->format('dmY');
            $fechaFinFormateada = $fechaFin ? $fechaFin->format('dmY') : now()->format('dmY');
            $hasFilters = $this->zipDownloadJob->tipo_dte || $this->zipDownloadJob->documento_emisor || $this->zipDownloadJob->busqueda;

            $zipFileName = "dtes_recibidos_{$fechaInicioFormateada}_{$fechaFinFormateada}" .
                ($hasFilters ? "_filtrado" : "") . ".zip";

            $s3Path = "zip_downloads_recibidos/{$this->business->id}/{$zipFileName}";

            $tempZipPath = sys_get_temp_dir() . '/' . uniqid('zip_recibidos_') . '.zip';

            $zip = new ZipArchive();
            if ($zip->open($tempZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \Exception('No se pudo crear el archivo ZIP temporal');
            }

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
            foreach ($dtes as $dte) {
                $jobs[] = new ProcessSingleReceivedDteForZip(
                    $this->zipDownloadJob->id,
                    $dte,
                    $tempZipPath
                );
            }

            $zipJobId = $this->zipDownloadJob->id;
            $tempPath = $tempZipPath;

            Bus::batch($jobs)
                ->name("ZIP Recibidos #{$zipJobId}")
                ->allowFailures()
                ->catch(function (Batch $batch, \Throwable $e) use ($zipJobId, $tempPath) {
                    Log::error("Batch fallido para ZIP Recibidos ID {$zipJobId}: " . $e->getMessage());
                    if (file_exists($tempPath)) {
                        unlink($tempPath);
                    }
                })
                ->dispatch();

            Log::info("Batch creado con {$totalDtes} jobs para ZIP Recibidos ID {$zipJobId}");
        } catch (\Exception $e) {
            Log::error("Error generando ZIP recibidos: " . $e->getMessage());

            $this->zipDownloadJob->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Job fallido para ZIP Recibidos ID {$this->zipDownloadJob->id}: " . $exception->getMessage());

        $this->zipDownloadJob->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
            'completed_at' => now(),
        ]);
    }
}
