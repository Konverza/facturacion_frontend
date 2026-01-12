<?php

namespace App\Jobs;

use App\Models\DteImportProcess;
use App\Services\HaciendaService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DownloadDtesFromHacienda implements ShouldQueue
{
    use Queueable;

    public $timeout = 120; // 2 minutos para la descarga
    public $tries = 3;

    protected $importProcess;
    protected $nit;

    /**
     * Create a new job instance.
     */
    public function __construct(DteImportProcess $importProcess, string $nit)
    {
        $this->importProcess = $importProcess;
        $this->nit = $nit;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Marcar como descargando
            $this->importProcess->markAsDownloading();

            // Obtener DTEs desde Hacienda
            $haciendaService = new HaciendaService($this->nit);
            $dtes = $haciendaService->fetchDtes();

            // Caso especial: respuesta exitosa pero sin DTEs
            if (empty($dtes)) {
                Log::info("Importación completada sin DTEs", [
                    'nit' => $this->nit,
                    'message' => 'Hacienda respondió exitosamente pero no hay documentos recibidos'
                ]);
                
                $this->importProcess->update([
                    'total_dtes' => 0,
                    'processed_dtes' => 0,
                    'failed_dtes' => 0,
                ]);
                $this->importProcess->markAsCompleted();
                return;
            }

            // Generar nombre del archivo
            $timestamp = now()->format('YmdHis');
            $filename = "dtes/{$this->nit}_{$timestamp}.json";

            // Guardar en S3
            Storage::disk('s3')->put(
                $filename,
                json_encode($dtes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            );

            // Actualizar proceso con el nombre del archivo y total de DTEs
            $totalDtes = count($dtes);
            $this->importProcess->update([
                'filename' => $filename,
            ]);
            $this->importProcess->markAsProcessing($totalDtes);

            Log::info("DTEs descargados y guardados en S3", [
                'nit' => $this->nit,
                'filename' => $filename,
                'total_dtes' => $totalDtes,
            ]);

            // Despachar jobs individuales para procesar cada DTE
            foreach ($dtes as $index => $dte) {
                ProcessDteFromS3::dispatch($this->importProcess, $dte, $index);
            }

        } catch (\Exception $e) {
            Log::error("Error al descargar DTEs desde Hacienda", [
                'nit' => $this->nit,
                'error' => $e->getMessage(),
            ]);
            $this->importProcess->markAsFailed($e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $this->importProcess->markAsFailed($exception->getMessage());
    }
}
