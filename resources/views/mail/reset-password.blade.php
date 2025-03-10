<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Facturación Electrónica Konverza</title>
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
</head>

<body>
    <h1>Restablecer contraseña</h1>
    <p>Estimado(a) {{ $name }},</p>
    <p>
        Recibió este correo electrónico porque recibimos una solicitud de restablecimiento de contraseña para su cuenta.
    </p>
    <p>
        Haga clic en el siguiente enlace para restablecer su contraseña:
        <a href="{{ $url }}">Restablecer contraseña</a>
    </p>
</body>

</html>
