<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessProductPriceVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_product_id',
        'price_variant_id',
        'price_without_iva',
        'price_with_iva',
    ];

    protected $casts = [
        'price_without_iva' => 'decimal:8',
        'price_with_iva' => 'decimal:8',
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
