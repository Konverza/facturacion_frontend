<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Imprimir ticket</title>
</head>
<body>
    <script>
        window.open(@json($ticketUrl), '_blank');
        window.location.href = @json($dashboardUrl);
    </script>
</body>
</html>
