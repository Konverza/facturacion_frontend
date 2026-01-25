<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ReceivedZipDownloadJob extends Model
{
    use HasFactory;

    protected $table = 'received_zip_download_jobs';

    protected $fillable = [
        'business_id',
        'fecha_inicio',
        'fecha_fin',
        'tipo_dte',
        'documento_emisor',
        'busqueda',
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

    public function fileExists(): bool
    {
        return $this->file_path && Storage::disk('s3')->exists($this->file_path);
    }

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

    public function getProgressPercentage(): int
    {
        if ($this->total_dtes === 0) {
            return 0;
        }

        return min(100, (int) (($this->processed_dtes / $this->total_dtes) * 100));
    }

    public function isInProgress(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    public static function hasActiveJobForBusiness(int $businessId): bool
    {
        return self::where('business_id', $businessId)
            ->whereIn('status', ['pending', 'processing'])
            ->exists();
    }

    public static function getActiveJobForBusiness(int $businessId): ?self
    {
        return self::where('business_id', $businessId)
            ->whereIn('status', ['pending', 'processing'])
            ->first();
    }

    public function deleteFile(): void
    {
        if ($this->file_path && Storage::disk('s3')->exists($this->file_path)) {
            Storage::disk('s3')->delete($this->file_path);
        }
    }

    public function getFiltersDescription(): string
    {
        $filters = [];

        $filters[] = "Fecha de Emisión: " . $this->fecha_inicio->format('d/m/Y') . " - " . $this->fecha_fin->format('d/m/Y');

        if ($this->tipo_dte) {
            $tipos = [
                '01' => 'Factura Consumidor Final',
                '03' => 'Comprobante de crédito fiscal',
                '04' => 'Nota de Remisión',
                '05' => 'Nota de crédito',
                '06' => 'Nota de débito',
                '07' => 'Comprobante de retención',
                '08' => 'Comprobante de liquidación',
                '09' => 'Documento Contable de Liquidación',
                '11' => 'Factura de exportación',
                '14' => 'Factura de sujeto excluido',
                '15' => 'Comprobante de Donación'
            ];
            $filters[] = "Tipo de DTE: " . ($tipos[$this->tipo_dte] ?? $this->tipo_dte);
        }

        if ($this->documento_emisor) {
            $filters[] = "Documento Emisor: " . $this->documento_emisor;
        }

        if ($this->busqueda) {
            $filters[] = "Búsqueda: " . $this->busqueda;
        }

        return implode("\n", $filters);
    }
}
