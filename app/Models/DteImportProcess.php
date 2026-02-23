<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        
        // Si ya procesamos todos, marcar como completado
        if ($this->processed_dtes >= $this->total_dtes) {
            $this->markAsCompleted();
        }
    }

    public function incrementFailed()
    {
        $this->increment('failed_dtes');
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
}
