<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>Documento Tributario Electrónico</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; margin: 0; padding: 0;">
    <table align="center" width="600" cellpadding="0" cellspacing="0"
        style="background-color: #ffffff; border: 1px solid #ccc; margin-top: 20px;">
        <tr>
            <td style="background-color: #002d87; padding: 20px; text-align: center;">
                <div style="background: #fff; border-radius: 10px; padding: 15px; display: inline-block;">
                    @if($logo)
                        <img src="{{$logo["url"]}}" alt="{{$emisor}}" width="200"
                        style="margin-bottom: 10px;" />
                    @endif
                </div>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px; text-align: center;">
                <h2 style="margin: 0; color: #333;">Documento Tributario Electrónico</h2>
                <p style="margin: 5px 0;">Estimado cliente: <strong>{{ $receptor }}</strong></p>
                <p style="margin: 10px 0;">Adjunto encontrará su documento tributario electrónico, emitido por:
                    <strong>{{ $emisor }}</strong>
                </p>
            </td>
        </tr>
        <tr>
            <td style="padding: 0 20px;">
                <table width="100%" cellpadding="10" cellspacing="0" style="border-collapse: collapse;">
                    <tr style="background-color: #f0f0f0;">
                        <td width="40%"><strong>Tipo:</strong></td>
                        <td>{{ $tipo_dte }}</td>
                    </tr>
                    <tr>
                        <td><strong>Número de control:</strong></td>
                        <td>{{ $numero_control }}</td>
                    </tr>
                    <tr style="background-color: #f0f0f0;">
                        <td><strong>Código de generación:</strong></td>
                        <td>{{ $codigo_generacion }}</td>
                    </tr>
                    <tr>
                        <td><strong>Sello de Recibido:</strong></td>
                        <td>{{ $sello_recibido }}</td>
                    </tr>
                    <tr style="background-color: #f0f0f0;">
                        <td><strong>NIT Emisor:</strong></td>
                        <td>{{ $nit_emisor }}</td>
                    </tr>
                    <tr>
                        <td><strong>Fecha de Emisión:</strong></td>
                        <td>{{ $fecha_emision }}</td>
                    </tr>
                    <tr style="background-color: #f0f0f0;">
                        <td><strong>Total:</strong></td>
                        <td>{{$total}}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px; text-align: center;">
                <a href="{{ Route('consulta.show',  $codigo_generacion) }}"
                    style="background-color: #002d87; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Consultar
                    DTE</a>
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
                            <p>Documento Emitido y Transmitido por:<br><strong>Konverza Digital</strong></p>
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