<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Facturación Electrónica Konverza</title>
</head>

<body>
    <p>Estimado(a) {{ $nombre }},</p>
    <p>Gracias por registrarse en Konverza. A continuación, le presentamos sus credenciales de acceso:</p>
    <p>Correo Electrónico: {{ $correo }}</p>
    <p>Contraseña: {{ $contrasena }}</p>
    <p>Para ingresar a su cuenta, haga clic en el siguiente enlace: <a href="{{env("APP_URL")}}">Ingresar</a></p>
    <p>Atentamente,</p>
    <p>Equipo de Konverza</p>

    <style>
        body {
            font-family: Arial, sans-serif;
        }

        p {
            font-size: 16px;
        }

        a {
            color: #3490dc;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</body>

</html>
