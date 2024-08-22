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
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Registro en Facturación Electrónica')
            ->view('mail.usuario')
            ->with([
                'nombre' => $this->nombre,
                'correo' => $this->email,
                'contrasena' => $this->password
            ]);
    }
}
