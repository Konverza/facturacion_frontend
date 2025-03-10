<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessProductMovement extends Model
{
    protected $fillable = [
        'business_product_id',
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
}
