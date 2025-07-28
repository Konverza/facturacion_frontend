<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageAttachment extends Model
{
    protected $fillable = [
        'ticket_message_id',
        'file_path',
    ];

    /**
     * Get the ticket message that owns the attachment.
     */
    public function ticketMessage()
    {
        return $this->belongsTo(TicketMessage::class, 'ticket_message_id');
    }
}
