<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'user_id',
        'subject',
        'message',
        'status',
    ];

    /**
     * Get the user that owns the ticket.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
