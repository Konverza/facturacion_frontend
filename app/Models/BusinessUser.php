<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessUser extends Model
{
    use HasFactory;

    protected $table = 'business_user';

    protected $fillable = [
        'business_id',
        'user_id',
        'role',
        'default_pos_id',
        'only_default_pos',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function defaultPos()
    {
        return $this->belongsTo(PuntoVenta::class, 'default_pos_id');
    }
}
