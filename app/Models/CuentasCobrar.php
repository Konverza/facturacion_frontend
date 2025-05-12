<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuentasCobrar extends Model
{
    protected $table = 'cuentas_por_cobrar';
    protected $fillable = [
        'numero_factura',
        'cliente',
        'monto',
        'saldo',
        'estado',
        'fecha_vencimiento',
        'observaciones',
        'business_id',
    ];

    public function movements()
    {
        return $this->hasMany(Movements::class, 'cuenta_id');
    }

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }
}
