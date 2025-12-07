<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>Restablecer contraseña - Konverza</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; margin: 0; padding: 0;">
    <table align="center" width="600" cellpadding="0" cellspacing="0"
        style="background-color: #ffffff; border: 1px solid #ccc; margin-top: 20px;">
        <tr>
            <td style="background-color: #002d87; padding: 20px; text-align: center;">
                <div style="background: #fff; border-radius: 10px; padding: 15px; display: inline-block;">
                    <img src="https://facturacion-pruebas.konverza.digital/images/only-icon.png" alt="Konverza" width="200"
                        style="margin-bottom: 10px;" />
                </div>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px;">
                <h2 style="color: #002d87; margin-top: 0;">Restablecer contraseña</h2>
                
                <p style="font-size: 16px; color: #333; line-height: 1.6;">Estimado(a) <strong>{{ $name }}</strong>,</p>
                
                <p style="font-size: 16px; color: #333; line-height: 1.6;">
                    Recibió este correo electrónico porque recibimos una solicitud de restablecimiento de contraseña para su cuenta.
                </p>

                <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; border-radius: 4px;">
                    <p style="margin: 0; font-size: 14px; color: #92400e;">
                        <strong>⚠️ Importante:</strong> Si no solicitó este restablecimiento, ignore este mensaje.
                    </p>
                </div>

                <p style="font-size: 16px; color: #333; line-height: 1.6;">
                    Haga clic en el siguiente botón para restablecer su contraseña:
                </p>

                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ $url }}" 
                       style="display: inline-block; background-color: #002d87; color: #ffffff; padding: 14px 30px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px;">
                        Restablecer Contraseña
                    </a>
                </div>

                <p style="font-size: 14px; color: #666; line-height: 1.6; border-top: 1px solid #e5e5e5; padding-top: 15px;">
                    Si tiene problemas con el botón, copie y pegue el siguiente enlace en su navegador:
                    <br>
                    <a href="{{ $url }}" style="color: #002d87; word-break: break-all;">{{ $url }}</a>
                </p>

                <p style="font-size: 16px; color: #333; line-height: 1.6; margin-top: 30px;">
                    Atentamente,<br>
                    <strong>Equipo de Konverza</strong>
                </p>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px; font-size: 12px; color: #555;">
                <p><strong>AVISO DE CONFIDENCIALIDAD:</strong> Este mensaje y documentos adjuntos contienen información
                    confidencial y/o privilegiada; pudiendo ser utilizada única y exclusivamente por el destinatario. Si
                    usted no es el receptor autorizado, tiene prohibida la publicación, revelación, copia, distribución,
                    reproducción o divulgación de esta información; por favor reenviarlo al remitente y borrar el mensaje recibido
                    inmediatamente.</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 10px 20px; font-size: 12px; color: #555;">
                <table style="width: 100%; border-top: 1px solid #ccc; padding-top: 10px;">
                    <tr>
                        <td>
                            <p>Correo enviado por:<br><strong>Konverza Digital</strong></p>
                        </td>
                        <td style="text-align: right; vertical-align: top; padding-bottom: 20px;">
                            <img src="https://facturacion-pruebas.konverza.digital/images/only-icon.png" alt="Konverza"
                                width="80" />
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
