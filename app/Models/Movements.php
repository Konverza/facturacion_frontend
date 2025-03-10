<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movements extends Model
{
    protected $table = 'movements';

    protected $casts = [
        'fecha' => 'datetime'
    ];

    protected $fillable = [
        'cuenta_id',
        'numero_factura',
        'tipo',
        'fecha',
        'monto',
        'observaciones'
    ];

    public function cuenta()
    {
        return $this->belongsTo(CuentasCobrar::class, 'cuenta_id');
    }
}
