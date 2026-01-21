<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;

    protected $table = 'business';

    protected $fillable = [
        'nit',
        'nombre',
        'plan_id',
        'dui',
        'telefono',
        'correo_responsable',
        'nombre_responsable',
        'posmode',
        'show_special_prices',
        'price_variants_enabled',
        'has_customer_branches',
        'pos_inventory_enabled',
        'active',
    ];

    protected $casts = [
        'posmode' => 'boolean',
        'show_special_prices' => 'boolean',
        'price_variants_enabled' => 'boolean',
        'has_customer_branches' => 'boolean',
        'pos_inventory_enabled' => 'boolean',
        'active' => 'boolean',
    ];

    public function priceVariants()
    {
        return $this->hasMany(BusinessPriceVariant::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function users()
    {
        return $this->hasMany(BusinessUser::class);
    }

    public function sucursales()
    {
        return $this->hasMany(Sucursal::class, 'business_id');
    }

    public function products()
    {
        return $this->hasMany(BusinessProduct::class, 'business_id');
    }
}
