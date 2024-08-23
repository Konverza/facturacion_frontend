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
    ];
}
