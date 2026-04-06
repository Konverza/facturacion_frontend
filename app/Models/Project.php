<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'business_id',
        'user_id',
        'quotation_id',
        'name',
        'status',
        'content',
        'comparison_meta',
        'costing_meta',
    ];

    protected $casts = [
        'content' => 'array',
        'comparison_meta' => 'array',
        'costing_meta' => 'array',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_id');
    }
}
