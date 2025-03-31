<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessProduct extends Model
{
    use HasFactory;

    protected $table = 'business_product';

    protected $fillable = [
        'business_id',
        'tipoItem',
        'codigo',
        'uniMedida',
        'descripcion',
        'precioUni',
        'precioSinTributos',
        'tributos',
        'stockInicial',
        'stockActual',
        'stockMinimo',
        'estado_stock',
        'has_stock'
    ];

    protected $casts = [
        'has_stock' => 'boolean'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function movements()
    {
        return $this->hasMany(BusinessProductMovement::class);
    }
}
