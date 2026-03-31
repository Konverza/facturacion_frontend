<?php

namespace App\Jobs;

use App\Models\DteImportProcess;
use App\Services\HaciendaService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessDteFromS3 implements ShouldQueue
{
    use Queueable;

    public $timeout = 30; // 30 segundos máximo por DTE
    public $tries = 2;

    protected $importProcess;
    protected $dte;
    protected $index;

    /**
     * Create a new job instance.
     */
    public function __construct(DteImportProcess $importProcess, array $dte, int $index)
    {
        $this->importProcess = $importProcess;
        $this->dte = $dte;
        $this->index = $index;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Obtener NIT del proceso
            $nit = $this->importProcess->nit;

            // Enviar DTE a Octopus usando el servicio
            $haciendaService = new HaciendaService($nit);
            $response = $haciendaService->sendDteToOctopus($this->dte, $nit);

            // Verificar respuesta
            if (isset($response['error']) && $response['error']) {
                // Marcar como fallido pero continuar con los demás
                $this->importProcess->incrementFailed();

                $this->importProcess->addProcessingError([
                    'index' => $this->index,
                    'codigo_generacion' => $this->dte['codigoGeneracion'] ?? 'N/A',
                    'error' => $response['message'] ?? 'Error desconocido',
                ]);

            } else {
                // Incrementar contador de procesados
                $this->importProcess->incrementProcessed();
            }

        } catch (\Exception $e) {

            // Marcar como fallido
            $this->importProcess->incrementFailed();

            $this->importProcess->addProcessingError([
                'index' => $this->index,
                'codigo_generacion' => $this->dte['codigoGeneracion'] ?? 'N/A',
                'error' => $e->getMessage(),
            ]);

            // No re-lanzar la excepción para no detener el proceso completo
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $this->importProcess->incrementFailed();

        $this->importProcess->addProcessingError([
            'index' => $this->index,
            'codigo_generacion' => $this->dte['codigoGeneracion'] ?? 'N/A',
            'error' => $exception->getMessage(),
        ]);
    }
}
