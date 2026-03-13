<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $table = 'dtes';

    protected $fillable = [
        'business_id',
        'user_id',
        'content',
        'name',
        'type',
        'status',
        'error_message',
        'is_quotation',
        'quotation_meta',
        'linked_dte_code',
    ];

    protected $casts = [
        'content' => 'array',
        'quotation_meta' => 'array',
        'is_quotation' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('quotation', function (Builder $builder) {
            $builder->where('is_quotation', true);
        });

        static::creating(function (Quotation $quotation) {
            $quotation->is_quotation = true;
            $quotation->status = $quotation->status ?? 'pending';
            $quotation->type = $quotation->type ?? '01';
        });
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
