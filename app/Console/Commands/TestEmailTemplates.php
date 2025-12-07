<?php

namespace App\Console\Commands;

use App\Mail\CustomNotificationMail;
use App\Mail\ResetPasswordEmail;
use App\Mail\UsuarioMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailTemplates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {type} {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar correos de prueba para visualizar las plantillas. Tipos: usuario, reset-password, notification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type');
        $email = $this->argument('email');

        // Validar email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('El correo electrónico no es válido.');
            return 1;
        }

        $this->info("Enviando correo de prueba tipo '{$type}' a: {$email}");

        try {
            switch ($type) {
                case 'usuario':
                    $this->sendUsuarioMail($email);
                    break;
                
                case 'reset-password':
                    $this->sendResetPasswordMail($email);
                    break;
                
                case 'notification':
                    $this->sendNotificationMail($email);
                    break;
                
                default:
                    $this->error("Tipo de correo no válido. Tipos disponibles: usuario, reset-password, notification");
                    return 1;
            }

            $this->info('✓ Correo enviado exitosamente!');
            return 0;

        } catch (\Exception $e) {
            $this->error('Error al enviar el correo: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Enviar correo de prueba tipo Usuario (credenciales)
     */
    private function sendUsuarioMail($email)
    {
        $nombre = 'Usuario de Prueba';
        $password = 'Prueba123!@#';

        Mail::to($email)->send(new UsuarioMail($nombre, $email, $password));
    }

    /**
     * Enviar correo de prueba tipo Reset Password
     */
    private function sendResetPasswordMail($email)
    {
        $token = 'token123456789';
        $url = url('/reset-password/' . $token);
        $name = 'Usuario de Prueba';

        Mail::to($email)->send(new ResetPasswordEmail($token, $url, $name));
    }

    /**
     * Enviar correo de prueba tipo Notificación Personalizada
     */
    private function sendNotificationMail($email)
    {
        $subject = 'Notificación de Prueba - Sistema Konverza';
        $content = '
            <h3 style="color: #002d87;">Este es un correo de prueba</h3>
            <p>Hola, este es un ejemplo de notificación personalizada del sistema.</p>
            <ul>
                <li>Característica 1: Texto con formato HTML</li>
                <li>Característica 2: Diseño responsivo</li>
                <li>Característica 3: Compatible con todos los clientes de correo</li>
            </ul>
            <p style="margin-top: 20px;">
                <strong>Nota:</strong> Este es un correo de prueba para verificar el diseño de la plantilla.
            </p>
        ';

        Mail::to($email)->send(new CustomNotificationMail($subject, $content));
    }
}
