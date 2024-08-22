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
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
