<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UsuarioMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $nombre;
    protected $email;
    protected $password;

    /**
     * Create a new message instance.
     */
    public function __construct($nombre, $email, $password)
    {
        $this->nombre = $nombre;
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * Get the message envelope.
     */
    public function build()
    {
        return $this->subject('Registro en facturación electrónica')
            ->view('mail.usuario')
            ->with([
                'name' => $this->nombre,
                'email' => $this->email,
                'password' => $this->password
            ]);
    }
}
