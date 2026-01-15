<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ZipDownloadJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'fecha_inicio',
        'fecha_fin',
        'status',
        'total_dtes',
        'processed_dtes',
        'file_path',
        'file_name',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Verifica si el archivo ZIP existe en S3
     */
    public function fileExists(): bool
    {
        return $this->file_path && Storage::disk('s3')->exists($this->file_path);
    }

    /**
     * Obtiene la URL temporal del archivo desde S3 (válida por 1 hora)
     */
    public function getFileUrl(): ?string
    {
        if (!$this->fileExists()) {
            return null;
        }
        
        return Storage::disk('s3')->temporaryUrl(
            $this->file_path,
            now()->addHour()
        );
    }

    /**
     * Calcula el progreso en porcentaje
     */
    public function getProgressPercentage(): int
    {
        if ($this->total_dtes === 0) {
            return 0;
        }
        
        return min(100, (int) (($this->processed_dtes / $this->total_dtes) * 100));
    }

    /**
     * Verifica si el trabajo está en progreso
     */
    public function isInProgress(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    /**
     * Verifica si hay un trabajo activo para una empresa
     */
    public static function hasActiveJobForBusiness(int $businessId): bool
    {
        return self::where('business_id', $businessId)
            ->whereIn('status', ['pending', 'processing'])
            ->exists();
    }

    /**
     * Obtiene el trabajo activo para una empresa
     */
    public static function getActiveJobForBusiness(int $businessId): ?self
    {
        return self::where('business_id', $businessId)
            ->whereIn('status', ['pending', 'processing'])
            ->first();
    }

    /**
     * Elimina el archivo ZIP asociado desde S3
     */
    public function deleteFile(): void
    {
        if ($this->file_path && Storage::disk('s3')->exists($this->file_path)) {
            Storage::disk('s3')->delete($this->file_path);
        }
    }
}
