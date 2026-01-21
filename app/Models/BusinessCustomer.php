<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessCustomer extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'tipoDocumento',
        'numDocumento',
        'nrc',
        'nombre',
        'codActividad',
        'nombreComercial',
        'departamento',
        'municipio',
        'complemento',
        'telefono',
        'correo',
        'codPais',
        'tipoPersona',
        'special_price',
        'price_variant_id',
        'use_branches',
    ];

    protected $casts = [
        'special_price' => 'boolean',
        'use_branches' => 'boolean',
    ];

    public function priceVariant()
    {
        return $this->belongsTo(BusinessPriceVariant::class, 'price_variant_id');
    }

    public function branches()
    {
        return $this->hasMany(BusinessCustomersBranch::class, 'business_customers_id');
    }
}
