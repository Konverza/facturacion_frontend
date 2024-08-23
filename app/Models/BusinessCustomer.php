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
    ];
}
