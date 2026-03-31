<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DteImportProcess extends Model
{
    protected $fillable = [
        'nit',
        'dui',
        'status',
        'filename',
        'total_dtes',
        'processed_dtes',
        'failed_dtes',
        'started_at',
        'completed_at',
        'error_message',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function getProgressPercentageAttribute()
    {
        if ($this->total_dtes === 0) {
            $fetchTotalUnits = (int) data_get($this->metadata, 'fetch.total_units', 0);
            $fetchCompletedUnits = (int) data_get($this->metadata, 'fetch.completed_units', 0);

            if ($fetchTotalUnits > 0) {
                return round(($fetchCompletedUnits / $fetchTotalUnits) * 100, 2);
            }

            return 0;
        }
        return round(($this->processed_dtes / $this->total_dtes) * 100, 2);
    }

    public function isInProgress()
    {
        return in_array($this->status, ['pending', 'downloading', 'processing']);
    }

    public function markAsDownloading()
    {
        $this->update([
            'status' => 'downloading',
            'started_at' => now(),
        ]);
    }

    public function markAsProcessing($totalDtes)
    {
        $this->update([
            'status' => 'processing',
            'total_dtes' => $totalDtes,
        ]);
    }

    public function incrementProcessed()
    {
        $this->increment('processed_dtes');
        $this->checkIfFinished();
    }

    public function incrementFailed()
    {
        $this->increment('failed_dtes');
        $this->checkIfFinished();
    }

    public function addProcessingError(array $error): void
    {
        DB::transaction(function () use ($error) {
            $lockedProcess = self::whereKey($this->getKey())->lockForUpdate()->first();

            if (!$lockedProcess) {
                return;
            }

            $metadata = $lockedProcess->metadata ?? [];
            $metadata['errors'] = $metadata['errors'] ?? [];
            $metadata['errors'][] = $error;

            $lockedProcess->update(['metadata' => $metadata]);
        });
    }

    public function initializeFetchTracking(array $fetchMetadata): void
    {
        DB::transaction(function () use ($fetchMetadata) {
            $lockedProcess = self::whereKey($this->getKey())->lockForUpdate()->first();

            if (!$lockedProcess) {
                return;
            }

            $metadata = $lockedProcess->metadata ?? [];
            $metadata['fetch'] = $fetchMetadata;
            $lockedProcess->update(['metadata' => $metadata]);
        });
    }

    public function registerFetchUnitResult(string $unitLabel, int $fetchedCount, ?string $lastFetchedAt = null, ?string $error = null): int
    {
        $startOffset = 0;

        DB::transaction(function () use ($unitLabel, $fetchedCount, $lastFetchedAt, $error, &$startOffset) {
            $lockedProcess = self::whereKey($this->getKey())->lockForUpdate()->first();

            if (!$lockedProcess) {
                return;
            }

            $startOffset = (int) $lockedProcess->total_dtes;

            $metadata = $lockedProcess->metadata ?? [];
            $fetch = $metadata['fetch'] ?? [];

            $fetch['pending_units'] = max(((int) ($fetch['pending_units'] ?? 0)) - 1, 0);
            $fetch['completed_units'] = ((int) ($fetch['completed_units'] ?? 0)) + 1;

            if (!empty($error)) {
                $fetch['failed_units'] = ((int) ($fetch['failed_units'] ?? 0)) + 1;
                $fetch['errors'] = $fetch['errors'] ?? [];
                $fetch['errors'][] = [
                    'unit' => $unitLabel,
                    'error' => $error,
                ];
            }

            if ($fetchedCount > 0) {
                $lockedProcess->total_dtes = $lockedProcess->total_dtes + $fetchedCount;
            }

            if (!empty($lastFetchedAt)) {
                $currentLastFetchedAt = $fetch['last_fetched_at'] ?? null;

                if (empty($currentLastFetchedAt) || strtotime($lastFetchedAt) > strtotime($currentLastFetchedAt)) {
                    $fetch['last_fetched_at'] = $lastFetchedAt;
                }
            }

            $metadata['fetch'] = $fetch;
            $lockedProcess->metadata = $metadata;
            $lockedProcess->save();
        });

        $this->checkIfFinished();

        return $startOffset;
    }

    public function registerFetchedDayResult(string $day, int $fetchedCount, ?string $error = null): int
    {
        return $this->registerFetchUnitResult($day, $fetchedCount, $day . ' 23:59:59', $error);
    }

    public function reserveFetchedDtes(int $count): int
    {
        $startOffset = 0;

        DB::transaction(function () use ($count, &$startOffset) {
            $lockedProcess = self::whereKey($this->getKey())->lockForUpdate()->first();

            if (!$lockedProcess) {
                return;
            }

            $startOffset = (int) $lockedProcess->total_dtes;

            if ($count > 0) {
                $lockedProcess->total_dtes = $lockedProcess->total_dtes + $count;
                $lockedProcess->save();
            }
        });

        return $startOffset;
    }

    public function incrementFetchFallbackCount(?string $unitLabel = null, ?string $error = null): void
    {
        DB::transaction(function () use ($unitLabel, $error) {
            $lockedProcess = self::whereKey($this->getKey())->lockForUpdate()->first();

            if (!$lockedProcess) {
                return;
            }

            $metadata = $lockedProcess->metadata ?? [];
            $fetch = $metadata['fetch'] ?? [];

            $fetch['fallback_to_daily_count'] = ((int) ($fetch['fallback_to_daily_count'] ?? 0)) + 1;

            if (!empty($error)) {
                $fetch['errors'] = $fetch['errors'] ?? [];
                $fetch['errors'][] = [
                    'unit' => $unitLabel ?? 'fallback',
                    'error' => $error,
                ];
            }

            $metadata['fetch'] = $fetch;
            $lockedProcess->update(['metadata' => $metadata]);
        });
    }

    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function markAsFailed($errorMessage)
    {
        $this->update([
            'status' => 'failed',
            'completed_at' => now(),
            'error_message' => $errorMessage,
        ]);
    }

    private function checkIfFinished(): void
    {
        $this->refresh();

        $pendingFetchUnits = (int) data_get($this->metadata, 'fetch.pending_units', 0);

        if ($pendingFetchUnits > 0) {
            return;
        }

        if (!in_array($this->status, ['pending', 'downloading', 'processing'])) {
            return;
        }

        if ($this->total_dtes === 0) {
            $this->markAsCompleted();
            return;
        }

        if (($this->processed_dtes + $this->failed_dtes) >= $this->total_dtes) {
            $this->markAsCompleted();
        }
    }
}
