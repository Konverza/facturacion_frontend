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
        'special_price',
        'special_price_with_iva',
        'cost',
        'margin',
        'precioSinTributos',
        'tributos',
        'stockInicial',
        'stockActual',
        'stockMinimo',
        'estado_stock',
        'has_stock',
        'image_url',
        'category_id',
    ];

    protected $casts = [
        'has_stock' => 'boolean',
        'tributos' => 'array',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function movements()
    {
        return $this->hasMany(BusinessProductMovement::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }
}
