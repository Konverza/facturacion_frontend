<?php

namespace App\Jobs;

use App\Models\ReceivedZipDownloadJob;
use App\Services\OctopusService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ProcessSingleReceivedDteForZip implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;
    public $tries = 2;

    protected $zipDownloadJobId;
    protected $dte;
    protected $tempZipPath;

    public function __construct(int $zipDownloadJobId, array $dte, string $tempZipPath)
    {
        $this->zipDownloadJobId = $zipDownloadJobId;
        $this->dte = $dte;
        $this->tempZipPath = $tempZipPath;
    }

    public function handle(): void
    {
        try {
            $zipDownloadJob = ReceivedZipDownloadJob::find($this->zipDownloadJobId);

            if (!$zipDownloadJob || $zipDownloadJob->status === 'failed') {
                return;
            }

            $codGeneracion = $this->dte['codGeneracion'] ?? null;
            $fhEmision = $this->dte['fhEmision'] ?? null;

            $fechaObj = $fhEmision ? new \DateTime($fhEmision) : new \DateTime();
            $fechaDia = $fechaObj->format('d-m-Y');

            $meses = [
                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
            ];
            $mesAnio = $meses[(int) $fechaObj->format('n')] . ' ' . $fechaObj->format('Y');

            $pdfContent = $this->buildPdfContent($this->dte, $codGeneracion);
            $jsonContent = $this->buildJsonContent($this->dte);

            $zip = new ZipArchive();
            $result = $zip->open($this->tempZipPath, ZipArchive::CREATE);

            if ($result !== true) {
                throw new \Exception("No se pudo abrir el archivo ZIP: {$result}");
            }

            $baseFolder = "{$mesAnio}/{$fechaDia}/{$codGeneracion}";

            if ($pdfContent) {
                $zip->addFromString("{$baseFolder}/{$codGeneracion}.pdf", $pdfContent);
            }
            if ($jsonContent) {
                $zip->addFromString("{$baseFolder}/{$codGeneracion}.json", $jsonContent);
            }

            $zip->close();
            $zipDownloadJob->increment('processed_dtes');
            $zipDownloadJob->refresh();

            if ($zipDownloadJob->processed_dtes >= $zipDownloadJob->total_dtes) {
                $this->uploadToS3($zipDownloadJob);
            }
        } catch (\Exception $e) {
            Log::error("Error procesando DTE recibido {$this->dte['codGeneracion']}: " . $e->getMessage());
            throw $e;
        }
    }

    protected function uploadToS3(ReceivedZipDownloadJob $zipDownloadJob): void
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

                Log::info("ZIP recibidos subido a S3: {$zipDownloadJob->file_name} con {$zipDownloadJob->processed_dtes} DTEs");
            } else {
                throw new \Exception('Error al subir archivo a S3');
            }
        } catch (\Exception $e) {
            Log::error("Error subiendo ZIP recibidos a S3: " . $e->getMessage());
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

    protected function buildJsonContent(array $dte): ?string
    {
        $raw = $dte['documento'] ?? null;

        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }

            return $raw;
        }

        if (is_array($raw) || is_object($raw)) {
            return json_encode($raw, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        return null;
    }

    protected function buildPdfContent(array $dte, ?string $codGeneracion): ?string
    {
        try {
            $octopus = new OctopusService();
            $catalogos = [
                'unidades_medidas' => $octopus->getCatalog("CAT-014"),
                'departamentos' => $octopus->simpleDepartamentos(),
                'tipos_documentos' => $octopus->getCatalog("CAT-022"),
                'actividades_economicas' => $octopus->getCatalog("CAT-019", null, true, true),
                'countries' => $octopus->getCatalog("CAT-020"),
                'recinto_fiscal' => $octopus->getCatalog("CAT-027", null, true, true),
                'regimen_exportacion' => $octopus->getCatalog("CAT-028", null, true, true),
                'tipos_establecimientos' => $octopus->getCatalog("CAT-009"),
                'formas_pago' => $octopus->getCatalog("CAT-017"),
                'tipo_servicio' => $octopus->getCatalog("CAT-010"),
                'modo_transporte' => $octopus->getCatalog("CAT-030"),
                'incoterms' => $octopus->getCatalog("CAT-031", null, true, true),
                'bienTitulo' => $octopus->getCatalog("CAT-025"),
            ];

            $pdf = Pdf::loadView('business.received_documents.pdf', [
                'dte' => $dte,
                'codGeneracion' => $codGeneracion,
                'catalogos' => $catalogos,
            ])->setPaper('letter', 'portrait');

            return $pdf->output();
        } catch (\Exception $e) {
            Log::error("Error generando PDF DTE recibido {$codGeneracion}: " . $e->getMessage());
            return null;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Job fallido para DTE recibido {$this->dte['codGeneracion']}: " . $exception->getMessage());

        $zipDownloadJob = ReceivedZipDownloadJob::find($this->zipDownloadJobId);
        if ($zipDownloadJob) {
            $zipDownloadJob->update([
                'status' => 'failed',
                'error_message' => "Error procesando DTE recibido {$this->dte['codGeneracion']}: " . $exception->getMessage(),
                'completed_at' => now(),
            ]);
        }
    }
}
