<?php

namespace App\Jobs;

use App\Models\DteImportProcess;
use App\Services\HaciendaService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class FetchDtesByDayFromHacienda implements ShouldQueue
{
    use Queueable;

    public $timeout = 600;
    public $tries = 1;
    public $failOnTimeout = true;

    protected $importProcess;
    protected $nit;
    protected $dui;
    protected $rangeLabel;
    protected $fechaDesde;
    protected $fechaHasta;
    protected $tipoDte;

    /**
     * Create a new job instance.
     */
    public function __construct(
        DteImportProcess $importProcess,
        string $nit,
        ?string $dui,
        string $rangeLabel,
        string $fechaDesde,
        string $fechaHasta,
        ?string $tipoDte = null
    ) {
        $this->importProcess = $importProcess;
        $this->nit = $nit;
        $this->dui = $dui;
        $this->rangeLabel = $rangeLabel;
        $this->fechaDesde = $fechaDesde;
        $this->fechaHasta = $fechaHasta;
        $this->tipoDte = $tipoDte;
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

        try {
            $haciendaService = new HaciendaService($this->nit, $this->dui);
            $weeklyResult = $haciendaService->fetchDtesWithMeta($this->fechaDesde, $this->fechaHasta, $this->tipoDte);

            if ($weeklyResult['success']) {
                $this->dispatchChunks($importProcess, $weeklyResult['dtes'], $this->rangeLabel, $this->fechaHasta);
                return;
            }

            $this->registerWeeklyFallback($importProcess, $weeklyResult['error'] ?? null);
            $this->fetchDailyFallback($importProcess, $haciendaService);

        } catch (\Throwable $e) {
            Log::warning('Fallo en obtencion por rango de DTEs', [
                'nit' => $this->nit,
                'range' => $this->rangeLabel,
                'error' => $e->getMessage(),
            ]);

            try {
                $this->registerWeeklyFallback($importProcess, $e->getMessage());
                $this->fetchDailyFallback($importProcess, new HaciendaService($this->nit, $this->dui));
            } catch (\Throwable $fallbackError) {
                Log::error('Fallo total en fallback diario de DTEs', [
                    'nit' => $this->nit,
                    'range' => $this->rangeLabel,
                    'error' => $fallbackError->getMessage(),
                ]);

                $importProcess->registerFetchUnitResult(
                    $this->rangeLabel,
                    0,
                    null,
                    $fallbackError->getMessage()
                );
            }
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        try {
            $importProcess = DteImportProcess::find($this->importProcess->id);

            if (!$importProcess || !$importProcess->isInProgress()) {
                return;
            }

            $importProcess->registerFetchUnitResult($this->rangeLabel, 0, null, $exception->getMessage());
        } catch (\Throwable $e) {
            Log::error('Error en callback failed() de FetchDtesByDayFromHacienda', [
                'nit' => $this->nit,
                'range' => $this->rangeLabel,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function dispatchChunks(
        DteImportProcess $importProcess,
        array $dtes,
        string $unitLabel,
        ?string $lastFetchedAt,
        ?string $error = null
    ): void
    {
        $fetchedCount = count($dtes);
        $startOffset = $importProcess->registerFetchUnitResult($unitLabel, $fetchedCount, $lastFetchedAt, $error);

        if ($fetchedCount === 0) {
            return;
        }

        $chunkSize = max((int) env('DTE_IMPORT_CHUNK_SIZE', 50), 1);

        for ($offset = 0; $offset < $fetchedCount; $offset += $chunkSize) {
            $dteChunk = array_slice($dtes, $offset, $chunkSize);

            ProcessDteChunkFromS3::dispatch(
                $importProcess,
                $dteChunk,
                $startOffset + $offset
            );
        }
    }

    private function registerWeeklyFallback(DteImportProcess $importProcess, ?string $error): void
    {
        $importProcess->incrementFetchFallbackCount($this->rangeLabel, $error);

        if (empty($error)) {
            return;
        }

        $importProcess->addProcessingError([
            'index' => null,
            'codigo_generacion' => 'N/A',
            'error' => "Fallback a diario para rango {$this->rangeLabel}: {$error}",
        ]);
    }

    private function fetchDailyFallback(DteImportProcess $importProcess, HaciendaService $haciendaService): void
    {
        $start = new \DateTimeImmutable($this->fechaDesde);
        $end = new \DateTimeImmutable($this->fechaHasta);
        $days = [];
        $cursor = $start;

        while ($cursor <= $end) {
            $day = $cursor->format('Y-m-d');
            $days[] = [
                'label' => $day,
                'from' => $day . ' 00:00:00',
                'to' => $day . ' 23:59:59',
            ];
            $cursor = $cursor->modify('+1 day');
        }

        $totalFetched = 0;
        $lastFetchedAt = null;
        $dailyErrors = [];

        foreach ($days as $dayRange) {
            try {
                $dailyResult = $haciendaService->fetchDtesWithMeta($dayRange['from'], $dayRange['to'], $this->tipoDte);
            } catch (\Throwable $e) {
                $dailyErrors[] = [
                    'day' => $dayRange['label'],
                    'error' => $e->getMessage(),
                ];
                continue;
            }

            if ($dailyResult['success']) {
                $dailyDtes = $dailyResult['dtes'];
                $dailyCount = count($dailyDtes);

                if ($dailyCount > 0) {
                    $startOffset = $importProcess->reserveFetchedDtes($dailyCount);
                    $this->dispatchChunksFromOffset($importProcess, $dailyDtes, $startOffset);
                    $totalFetched += $dailyCount;
                }

                $lastFetchedAt = $dayRange['to'];
                continue;
            }

            $dailyErrors[] = [
                'day' => $dayRange['label'],
                'error' => $dailyResult['error'] ?? 'Error desconocido en fallback diario.',
            ];
        }

        $errorText = null;

        if (!empty($dailyErrors)) {
            $errorText = 'Fallos en fallback diario: ' . json_encode($dailyErrors, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        $importProcess->registerFetchUnitResult(
            $this->rangeLabel . ' (fallback diario)',
            0,
            $lastFetchedAt,
            $errorText
        );

        Log::info('Fallback diario procesado por streaming', [
            'nit' => $this->nit,
            'range' => $this->rangeLabel,
            'total_fetched' => $totalFetched,
            'days_with_error' => count($dailyErrors),
        ]);
    }

    private function dispatchChunksFromOffset(DteImportProcess $importProcess, array $dtes, int $startOffset): void
    {
        $fetchedCount = count($dtes);

        if ($fetchedCount === 0) {
            return;
        }

        $chunkSize = max((int) env('DTE_IMPORT_CHUNK_SIZE', 50), 1);

        for ($offset = 0; $offset < $fetchedCount; $offset += $chunkSize) {
            $dteChunk = array_slice($dtes, $offset, $chunkSize);

            ProcessDteChunkFromS3::dispatch(
                $importProcess,
                $dteChunk,
                $startOffset + $offset
            );
        }
    }
}
