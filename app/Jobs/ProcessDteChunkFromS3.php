<?php

namespace App\Jobs;

use App\Models\DteImportProcess;
use App\Services\HaciendaService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessDteChunkFromS3 implements ShouldQueue
{
    use Queueable;

    public $timeout = 120;
    public $tries = 1;
    public $failOnTimeout = true;

    protected $importProcess;
    protected $dtes;
    protected $offset;

    /**
     * Create a new job instance.
     */
    public function __construct(DteImportProcess $importProcess, array $dtes, int $offset = 0)
    {
        $this->importProcess = $importProcess;
        $this->dtes = $dtes;
        $this->offset = $offset;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $importProcess = DteImportProcess::find($this->importProcess->id);

        if (!$importProcess || !$importProcess->isInProgress()) {
            return;
        }

        $nit = $importProcess->nit;
        $haciendaService = new HaciendaService($nit);

        foreach ($this->dtes as $index => $dte) {
            try {
                $response = $haciendaService->sendDteToOctopus($dte, $nit);

                if (isset($response['error']) && $response['error']) {
                    $importProcess->incrementFailed();
                    $importProcess->addProcessingError([
                        'index' => $this->offset + $index,
                        'codigo_generacion' => $dte['codigoGeneracion'] ?? 'N/A',
                        'error' => $response['message'] ?? 'Error desconocido',
                    ]);
                    continue;
                }

                $importProcess->incrementProcessed();
            } catch (\Throwable $e) {
                $importProcess->incrementFailed();
                $importProcess->addProcessingError([
                    'index' => $this->offset + $index,
                    'codigo_generacion' => $dte['codigoGeneracion'] ?? 'N/A',
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
