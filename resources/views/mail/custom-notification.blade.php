<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>{{ $subject }}</title>
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
                {!! $customContent !!}
            </td>
        </tr>
        <tr>
            <td style="padding: 20px; font-size: 12px; color: #555;">
                <p><strong>AVISO DE CONFIDENCIALIDAD:</strong> Este mensaje y documentos adjuntos contienen información
                    confidencial y/o privilegiada; pudiendo ser utilizada única y exclusivamente por el destinatario. Si
                    usted no
                    es el receptor autorizado, tiene prohibida la publicación, revelación, copia, distribución,
                    reproducción o
                    divulgación de esta información; por favor reenviarlo al remitente y borrar el mensaje recibido
                    inmediatamente.</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 10px 20px; font-size: 12px; color: #555;">
                <table style="width: 100%; border-top: 1px solid #ccc; padding-top: 10px;">
                    <tr>
                        <td>
                            <p>Notificación enviada por:<br><strong>Konverza Digital</strong></p>
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
