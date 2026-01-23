<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceBagInvoice extends Model
{
    use HasFactory;

    protected $table = 'invoice_bag_invoices';

    protected $fillable = [
        'invoice_bag_id',
        'business_id',
        'user_id',
        'invoice_uuid',
        'correlative',
        'status',
        'individual_converted',
        'omitted_receptor',
        'pos_id',
        'dte_id',
        'customer_data',
        'products',
        'totals',
        'dte_snapshot',
        'converted_at',
        'voided_at',
    ];

    protected $casts = [
        'individual_converted' => 'boolean',
        'omitted_receptor' => 'boolean',
        'customer_data' => 'array',
        'products' => 'array',
        'totals' => 'array',
        'dte_snapshot' => 'array',
        'converted_at' => 'datetime',
        'voided_at' => 'datetime',
    ];

    public function bag()
    {
        return $this->belongsTo(InvoiceBag::class, 'invoice_bag_id');
    }

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
