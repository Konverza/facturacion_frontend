<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessProductCostVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_product_id',
        'nombre_proveedor',
        'costo_final',
        'price_variant_id',
    ];

    protected $casts = [
        'costo_final' => 'decimal:8',
    ];

    public function product()
    {
        return $this->belongsTo(BusinessProduct::class, 'business_product_id');
    }

    public function priceVariant()
    {
        return $this->belongsTo(BusinessPriceVariant::class, 'price_variant_id');
    }
}
