<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Ticket Bolsón</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .ticket {
            width: 58mm;
        }
        h1 {
            font-size: 14px;
            margin: 0 0 6px;
            text-align: center;
        }
        .row {
            font-size: 10px;
            margin-bottom: 4px;
        }
        .divider {
            border-top: 1px solid #999;
            margin: 6px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }
        th, td {
            padding: 2px 0;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="ticket">
        <h1>Ticket de Compra</h1>
        <div class="row"><b>Bolsón:</b> {{ $invoice->bag->bag_code }}</div>
        <div class="row"><b>Factura:</b> #{{ $invoice->correlative }}</div>
        <div class="row"><b>ID:</b> {{ $invoice->invoice_uuid }}</div>
        <div class="row"><b>Fecha:</b> {{ $invoice->created_at->format('d/m/Y H:i') }}</div>
        <div class="divider"></div>
        <div class="row"><b>Cliente:</b> {{ data_get($invoice, 'customer_data.nombre_receptor', 'Consumidor final') }}</div>
        <div class="row"><b>Documento:</b> {{ data_get($invoice, 'customer_data.numero_documento', 'N/D') }}</div>
        <div class="divider"></div>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th class="text-right">Cant</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->products as $product)
                    <tr>
                        <td>{{ $product['descripcion'] ?? 'Producto' }}</td>
                        <td class="text-right">{{ $product['cantidad'] ?? 0 }}</td>
                        <td class="text-right">${{ number_format((float) ($product['total'] ?? 0), 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="divider"></div>
        <div class="row text-right"><b>Total:</b> ${{ number_format((float) ($invoice->totals['total_pagar'] ?? 0), 2) }}</div>
    </div>
    <script>
        window.print();
    </script>
</body>
</html>
