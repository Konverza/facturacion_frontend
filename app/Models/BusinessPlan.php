<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessPlan extends Model
{
    use HasFactory;

    protected $table = 'business_plan';

    protected $fillable = [
        'business_id',
        'plan_id',
        'dtes',
        'billing_type',
        'extra_dtes',
        'extra_dtes_expiration',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id', 'id');
    }
}
