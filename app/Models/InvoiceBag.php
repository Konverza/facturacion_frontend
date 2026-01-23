<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceBag extends Model
{
    use HasFactory;

    protected $table = 'invoice_bags';

    protected $fillable = [
        'business_id',
        'user_id',
        'bag_date',
        'correlative',
        'bag_code',
        'status',
        'sent_dte_codigo',
        'sent_at',
    ];

    protected $casts = [
        'bag_date' => 'date',
        'sent_at' => 'datetime',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function invoices()
    {
        return $this->hasMany(InvoiceBagInvoice::class, 'invoice_bag_id');
    }
}
