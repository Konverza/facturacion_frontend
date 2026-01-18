<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosTransferItem extends Model
{
    protected $fillable = [
        'pos_transfer_id',
        'business_product_id',
        'cantidad_solicitada',
        'cantidad_real',
        'diferencia',
        'nota_item',
    ];

    protected $casts = [
        'cantidad_solicitada' => 'decimal:2',
        'cantidad_real' => 'decimal:2',
        'diferencia' => 'decimal:2',
    ];

    /**
     * Relación con la transferencia
     */
    public function transfer()
    {
        return $this->belongsTo(PosTransfer::class, 'pos_transfer_id');
    }

    /**
     * Relación con el producto
     */
    public function businessProduct()
    {
        return $this->belongsTo(BusinessProduct::class, 'business_product_id');
    }

    /**
     * Calcular diferencia entre cantidad real y solicitada
     */
    public function calcularDiferencia(): void
    {
        if ($this->cantidad_real !== null) {
            $this->diferencia = $this->cantidad_real - $this->cantidad_solicitada;
            $this->save();
        }
    }

    /**
     * Verificar si hay discrepancia
     */
    public function tieneDiscrepancia(): bool
    {
        return $this->diferencia !== null && $this->diferencia != 0;
    }
}
