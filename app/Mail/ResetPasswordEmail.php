<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $url;
    public $name;

    /**
     * Create a new message instance.
     */
    public function __construct($token, $url, $name)
    {
        $this->token = $token;
        $this->url = $url;
        $this->name = $name;
    }

    public function build()
    {
        return $this->subject("Restablecer contraseña")
        ->view('mail.reset-password')
        ->with([
            'name' => $this->name,
            'url' => $this->url,
            'token' => $this->token,
        ]);
    }

}
