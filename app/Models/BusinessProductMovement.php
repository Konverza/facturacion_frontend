<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessProductMovement extends Model
{
    protected $fillable = [
        'business_product_id',
        'sucursal_id',
        'punto_venta_id',
        'numero_factura',
        'tipo',
        'cantidad',
        'precio_unitario',
        'producto',
        'descripcion'  
    ];

    public function businessProduct()
    {
        return $this->belongsTo(BusinessProduct::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function puntoVenta()
    {
        return $this->belongsTo(PuntoVenta::class, 'punto_venta_id');
    }
}
