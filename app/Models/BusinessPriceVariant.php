<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessPriceVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'price_without_iva',
        'price_with_iva',
    ];

    protected $casts = [
        'price_without_iva' => 'decimal:8',
        'price_with_iva' => 'decimal:8',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function productPrices()
    {
        return $this->hasMany(BusinessProductPriceVariant::class, 'price_variant_id');
    }
}
