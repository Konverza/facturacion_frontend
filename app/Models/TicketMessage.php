<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketMessage extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'is_read',
        'read_at',
    ];

    /**
     * Get the ticket that owns the message.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the user that sent the message.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the attachments for the message.
     */
    public function attachments()
    {
        return $this->hasMany(MessageAttachment::class, 'ticket_message_id');
    }
}
