<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>Bienvenido a Facturación Electrónica de Konverza</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; margin: 0; padding: 0;">
    <table align="center" width="600" cellpadding="0" cellspacing="0"
        style="background-color: #ffffff; border: 1px solid #ccc; margin-top: 20px;">
        <tr>
            <td style="background-color: #002d87; padding: 20px; text-align: center;">
                <div style="background: #fff; border-radius: 10px; padding: 15px; display: inline-block;">
                    <img src="https://facturacion-pruebas.konverza.digital/images/only-icon.png" alt="Konverza"
                        width="200" style="margin-bottom: 10px;" />
                </div>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px;">
                <h2 style="color: #002d87; margin-top: 0;">¡Bienvenido(a) a Facturación Electrónica de Konverza!</h2>

                <p style="font-size: 16px; color: #333; line-height: 1.6;">Estimado(a)
                    <strong>{{ $name }}</strong>,</p>

                <p style="font-size: 16px; color: #333; line-height: 1.6;">
                    Gracias por registrarse en Konverza. A continuación, le presentamos sus credenciales de acceso:
                </p>

                <table
                    style="width: 100%; margin: 20px 0; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
                    <tr>
                        <td style="padding: 15px; background-color: #f5f5f5; border-bottom: 1px solid #e0e0e0;">
                            <strong style="color: #555;">Correo Electrónico:</strong>
                        </td>
                        <td style="padding: 15px; background-color: #fff; border-bottom: 1px solid #e0e0e0;">
                            <span style="color: #333;">{{ $email }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 15px; background-color: #f5f5f5;">
                            <strong style="color: #555;">Contraseña:</strong>
                        </td>
                        <td style="padding: 15px; background-color: #fffbea;">
                            <code
                                style="font-family: 'Lucida Console', 'Courier New', monospace; font-size: 18px; font-weight: bold; color: #d97706; background-color: #fef3c7; padding: 8px 12px; border-radius: 4px; display: inline-block; letter-spacing: 1px;">{{ $password }}</code>
                        </td>
                    </tr>
                </table>

                <div
                    style="background-color: {{ env('AMBIENTE') == 2 ? '#dbeafe' : '#dcfce7' }}; border: 2px dashed {{ env('AMBIENTE') == 2 ? '#3b82f6' : '#22c55e' }}; border-radius: 8px; padding: 15px; margin: 20px 0; text-align: center;">
                    <p style="margin: 0; color: {{ env('AMBIENTE') == 2 ? '#1e40af' : '#166534' }}; font-size: 14px;">
                        <strong>Ambiente:</strong> {{ env('AMBIENTE') == 2 ? 'PRUEBAS' : 'PRODUCTIVO' }}
                    </p>
                </div>

                <p style="font-size: 16px; color: #333; line-height: 1.6;">
                    Para ingresar a su cuenta, acceda a:
                </p>

                <div style="text-align: center; margin: 25px 0;">
                    <a href="{{ env('APP_URL') }}"
                        style="display: inline-block; background-color: #002d87; color: #ffffff; padding: 14px 30px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px;">
                        Ingresar al Sistema
                    </a>
                    <p style="margin-top: 10px; font-size: 14px; color: #666;">
                        Si tiene problemas con el botón, copie y pegue el siguiente enlace en su navegador:
                        <br>
                        <a href="{{ env('APP_URL') }}"
                            style="color: #002d87; text-decoration: none;">{{ env('APP_URL') }}</a>
                    </p>
                </div>

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
                    reproducción o divulgación de esta información; por favor reenviarlo al remitente y borrar el
                    mensaje recibido
                    inmediatamente.</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 10px 20px; font-size: 12px; color: #555;">
                <table style="width: 100%; border-top: 1px solid #ccc; padding-top: 10px;">
                    <tr>
                        <td>
                            <p>Credenciales enviadas por:<br><strong>Konverza Digital</strong></p>
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
