<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessReportFilter extends Model
{
    protected $table = 'business_report_filters';

    protected $fillable = [
        'user_id',
        'business_id',
        'name',
        'filters',
    ];

    protected $casts = [
        'filters' => 'array',
    ];
}
