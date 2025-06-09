<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PuntoVenta extends Model
{
    protected $table = 'punto_ventas';

    protected $fillable = [
        'nombre',
        'codPuntoVenta',
        'sucursal_id'
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }
}
