<?php

namespace App\Jobs;

use App\Models\ZipDownloadJob;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ProcessSingleDteForZip implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;
    public $tries = 2;

    protected $zipDownloadJobId;
    protected $dte;
    protected $tempZipPath;

    /**
     * Create a new job instance.
     */
    public function __construct(int $zipDownloadJobId, array $dte, string $tempZipPath)
    {
        $this->zipDownloadJobId = $zipDownloadJobId;
        $this->dte = $dte;
        $this->tempZipPath = $tempZipPath;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $zipDownloadJob = ZipDownloadJob::find($this->zipDownloadJobId);
            
            if (!$zipDownloadJob || $zipDownloadJob->status === 'failed') {
                return;
            }

            $codGeneracion = $this->dte['codGeneracion'];
            $documento = json_decode($this->dte['documento']);
            $numeroControl = $documento->identificacion->numeroControl;
            $fhProcesamiento = $this->dte['fhProcesamiento'];

            $fechaObj = new \DateTime($fhProcesamiento);
            $fechaDia = $fechaObj->format('d-m-Y');

            $meses = [
                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
            ];
            $mesAnio = $meses[(int)$fechaObj->format('n')] . ' ' . $fechaObj->format('Y');

            $pdfContent = $this->dte["enlace_pdf"] ? $this->downloadFile($this->dte["enlace_pdf"]) : null;
            $jsonContent = $this->dte["enlace_json"] ? $this->downloadFile($this->dte["enlace_json"]) : null;
            $ticketContent = $this->dte["enlace_rtf"] ? $this->downloadFile($this->dte["enlace_rtf"]) : null;

            $zip = new ZipArchive();
            $result = $zip->open($this->tempZipPath, ZipArchive::CREATE);
            
            if ($result !== TRUE) {
                throw new \Exception("No se pudo abrir el archivo ZIP: {$result}");
            }

            $baseFolder = "{$mesAnio}/{$fechaDia}/{$numeroControl}";
            
            if ($pdfContent) {
                $zip->addFromString("{$baseFolder}/{$codGeneracion}.pdf", $pdfContent);
            }
            if ($jsonContent) {
                $zip->addFromString("{$baseFolder}/{$codGeneracion}.json", $jsonContent);
            }
            if ($ticketContent) {
                $zip->addFromString("{$baseFolder}/{$codGeneracion}_ticket.pdf", $ticketContent);
            }

            $zip->close();
            $zipDownloadJob->increment('processed_dtes');
            $zipDownloadJob->refresh();


            // Si este es el último DTE, subir a S3
            if ($zipDownloadJob->processed_dtes >= $zipDownloadJob->total_dtes) {
                $this->uploadToS3($zipDownloadJob);
            }

        } catch (\Exception $e) {
            Log::error("Error procesando DTE {$this->dte['codGeneracion']}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Sube el archivo ZIP a S3 y limpia el temporal
     */
    protected function uploadToS3(ZipDownloadJob $zipDownloadJob): void
    {
        try {
            if (!file_exists($this->tempZipPath)) {
                throw new \Exception("Archivo temporal no encontrado: {$this->tempZipPath}");
            }

            $s3Uploaded = Storage::disk('s3')->put(
                $zipDownloadJob->file_path,
                file_get_contents($this->tempZipPath)
            );
            
            if ($s3Uploaded) {
                if (file_exists($this->tempZipPath)) {
                    unlink($this->tempZipPath);
                }
                
                $zipDownloadJob->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
                
                Log::info("ZIP subido a S3 exitosamente: {$zipDownloadJob->file_name} con {$zipDownloadJob->processed_dtes} DTEs");
            } else {
                throw new \Exception('Error al subir archivo a S3');
            }
        } catch (\Exception $e) {
            Log::error("Error subiendo ZIP a S3: " . $e->getMessage());
            $zipDownloadJob->update([
                'status' => 'failed',
                'error_message' => 'Error al subir archivo a S3: ' . $e->getMessage(),
                'completed_at' => now(),
            ]);

            if (file_exists($this->tempZipPath)) {
                unlink($this->tempZipPath);
            }
            
            throw $e;
        }
    }

    /**
     * Descarga un archivo con reintentos
     */
    protected function downloadFile(string $url, int $maxRetries = 3): ?string
    {
        for ($i = 0; $i < $maxRetries; $i++) {
            try {
                $response = Http::timeout(30)->get($url);
                if ($response->successful()) {
                    return $response->body();
                }
            } catch (\Exception $e) {
                if ($i === $maxRetries - 1) {
                    throw $e;
                }
                sleep(1);
            }
        }
        
        return null;
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Job fallido para DTE {$this->dte['codGeneracion']}: " . $exception->getMessage());

        $zipDownloadJob = ZipDownloadJob::find($this->zipDownloadJobId);
        if ($zipDownloadJob) {
            $zipDownloadJob->update([
                'status' => 'failed',
                'error_message' => "Error procesando DTE {$this->dte['codGeneracion']}: " . $exception->getMessage(),
                'completed_at' => now(),
            ]);
        }
    }
}
