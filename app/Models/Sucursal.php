<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    protected $table = 'sucursals';

    protected $fillable = [
        'nombre',
        'departamento',
        'municipio',
        'complemento',
        'telefono',
        'correo',
        'codSucursal',
        'business_id'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function puntosVentas()
    {
        return $this->hasMany(PuntoVenta::class, 'sucursal_id');
    }
}
