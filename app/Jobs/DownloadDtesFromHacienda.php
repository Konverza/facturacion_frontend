<?php

namespace App\Jobs;

use App\Models\DteImportProcess;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Log;

class DownloadDtesFromHacienda implements ShouldQueue
{
    use Queueable;

    public $timeout = 120; // solo planifica y despacha jobs de rango
    public $tries = 1;
    public $failOnTimeout = true;

    protected $importProcess;
    protected $nit;
    protected $dui;
    protected $filters;

    /**
     * Create a new job instance.
     */
    public function __construct(DteImportProcess $importProcess, string $nit, ?string $dui = null, array $filters = [])
    {
        $this->importProcess = $importProcess;
        $this->nit = $nit;
        $this->dui = $dui;
        $this->filters = $filters;
    }

    /**
     * Evita que el mismo proceso se ejecute en paralelo por reintentos/visibilidad.
     */
    public function middleware(): array
    {
        return [
            (new WithoutOverlapping('dte-import-download-' . $this->importProcess->id))
                ->expireAfter($this->timeout + 120)
                ->dontRelease(),
        ];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Marcar como descargando
            $this->importProcess->markAsDownloading();

            [$startAt, $endAt] = $this->resolveDateRange();

            if ($startAt->greaterThan($endAt)) {
                $this->importProcess->markAsProcessing(0);
                $this->importProcess->initializeFetchTracking([
                    'start_at' => $startAt->format('Y-m-d H:i:s'),
                    'end_at' => $endAt->format('Y-m-d H:i:s'),
                    'tipo_dte' => $this->filters['tipo_dte'] ?? null,
                    'strategy' => 'weekly_then_daily_fallback',
                    'total_units' => 0,
                    'pending_units' => 0,
                    'completed_units' => 0,
                    'failed_units' => 0,
                    'fallback_to_daily_count' => 0,
                    'last_fetched_at' => null,
                ]);
                $this->importProcess->markAsCompleted();
                return;
            }

            $ranges = $this->buildWeeklyRanges($startAt, $endAt);
            $totalRanges = count($ranges);

            $this->importProcess->markAsProcessing(0);
            $this->importProcess->initializeFetchTracking([
                'start_at' => $startAt->format('Y-m-d H:i:s'),
                'end_at' => $endAt->format('Y-m-d H:i:s'),
                'tipo_dte' => $this->filters['tipo_dte'] ?? null,
                'strategy' => 'weekly_then_daily_fallback',
                'total_units' => $totalRanges,
                'pending_units' => $totalRanges,
                'completed_units' => 0,
                'failed_units' => 0,
                'fallback_to_daily_count' => 0,
                'last_fetched_at' => null,
            ]);

            if ($totalRanges === 0) {
                $this->importProcess->markAsCompleted();
                return;
            }

            $parallel = max((int) env('DTE_IMPORT_FETCH_PARALLEL', 4), 1);

            foreach ($ranges as $index => $range) {
                $wave = intdiv($index, $parallel);

                FetchDtesByDayFromHacienda::dispatch(
                    $this->importProcess,
                    $this->nit,
                    $this->dui,
                    $range['label'],
                    $range['from'],
                    $range['to'],
                    $this->filters['tipo_dte'] ?? null
                )->delay(now()->addSeconds($wave * 2));
            }

            Log::info('Plan semanal con fallback diario despachado', [
                'nit' => $this->nit,
                'total_ranges' => $totalRanges,
                'start_at' => $startAt->format('Y-m-d H:i:s'),
                'end_at' => $endAt->format('Y-m-d H:i:s'),
                'tipo_dte' => $this->filters['tipo_dte'] ?? null,
                'parallel' => $parallel,
            ]);

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

    private function resolveDateRange(): array
    {
        $explicitFrom = $this->filters['fecha_desde'] ?? null;
        $explicitTo = $this->filters['fecha_hasta'] ?? null;

        if (!empty($explicitFrom) && !empty($explicitTo)) {
            return [
                Carbon::parse($explicitFrom)->startOfDay(),
                Carbon::parse($explicitTo)->endOfDay(),
            ];
        }

        $latestCompleted = DteImportProcess::where('nit', $this->nit)
            ->where('status', 'completed')
            ->whereNotNull('metadata')
            ->latest('completed_at')
            ->first();

        $lastFetchedAt = data_get($latestCompleted?->metadata, 'fetch.last_fetched_at');

        if (!empty($lastFetchedAt)) {
            $start = Carbon::parse($lastFetchedAt)->addDay()->startOfDay();
        } else {
            $start = Carbon::create(2024, 1, 1, 0, 0, 0);
        }

        return [$start, now()->endOfDay()];
    }

    private function buildWeeklyRanges(Carbon $startAt, Carbon $endAt): array
    {
        $ranges = [];
        $current = $startAt->copy()->startOfDay();

        while ($current->lessThanOrEqualTo($endAt)) {
            $rangeStart = $current->copy();
            $rangeEnd = $current->copy()->addDays(6)->endOfDay();

            if ($rangeEnd->greaterThan($endAt)) {
                $rangeEnd = $endAt->copy();
            }

            $ranges[] = [
                'label' => $rangeStart->format('Y-m-d') . ' -> ' . $rangeEnd->format('Y-m-d'),
                'from' => $rangeStart->format('Y-m-d H:i:s'),
                'to' => $rangeEnd->format('Y-m-d H:i:s'),
            ];

            $current = $rangeEnd->copy()->addSecond()->startOfDay();
        }

        return $ranges;
    }
}
