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
        'branch_selector', // Indicates if the user can select branches
        'see_others_dtes',
        'can_edit_date',
    ];

    protected $casts = [
        'only_default_pos' => 'boolean',
        'branch_selector' => 'boolean',
        'see_others_dtes' => 'boolean',
        'can_edit_date' => 'boolean',
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
