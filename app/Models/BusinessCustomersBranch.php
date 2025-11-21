<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessCustomersBranch extends Model
{
    protected $fillable = [
        'business_customers_id',
        'branch_code',
        'nombre',
        'departamento',
        'municipio',
        'complemento',
    ];

    public function businessCustomer()
    {
        return $this->belongsTo(BusinessCustomer::class, 'business_customers_id');
    }
}
