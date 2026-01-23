<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Ticket {{ $invoice->correlative }}</title>
    <style>
        @page {
            margin: 5mm 5mm 0mm 2mm;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .ticket {
            width: 52mm;
            /* padding: 4mm 3mm 4mm 0; */
        }

        h1 {
            font-size: 12px;
            margin: 0 0 6px;
            text-align: center;
        }

        h2 {
            font-size: 10px;
            margin: 0 0 6px;
            text-align: center;
        }

        .row {
            font-size: 9px;
            margin-bottom: 3px;
        }

        .divider {
            border-top: 1px solid #999;
            margin: 6px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }

        th,
        td {
            padding: 2px 0;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="ticket">
        <h1>{{ $business_data['nombreComercial'] ?? $business_data['nombre'] }}</h1>
        <h2 class="text-center">{{ $business_data['complemento'] }}</h2>
        <div class="row text-center">NIT: {{ $business_data['nit'] }}</div>
        <div class="row text-center">NRC: {{ $business_data['nrc'] }}</div>
        <div class="row text-center">Giro: {{ $business_data['descActividad'] }}</div>
        <div class="divider"></div>
        <h2 class="text-center">NO ES UN DOCUMENTO FISCAL</h2>
        <div class="divider"></div>
        <div class="row"><b>Código:</b> {{ $invoice->invoice_uuid }}</div>
        <div class="row"><b>Fecha:</b> {{ $invoice->created_at->format('d/m/Y H:i') }}</div>
        <div class="divider"></div>
        <div class="row"><b>Cliente:</b>
            {{ data_get($invoice, 'customer_data.nombre_receptor', 'Consumidor final') }}</div>
        <div class="row"><b>Documento:</b> {{ data_get($invoice, 'customer_data.numero_documento', 'N/D') }}</div>
        <div class="divider"></div>
        <table>
            <thead>
                <tr>
                    <th width="15%" class="text-left">Cant</th>
                    <th width="65%" class="text-left">Producto</th>
                    <th width="20%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->products as $product)
                    <tr>
                        <td>{{ $product['cantidad'] ?? 0 }}</td>
                        <td>{{ substr($product['descripcion'], 0, 24) ?? 'Producto' }}</td>
                        <td class="text-right">${{ number_format((float) ($product['total'] ?? 0), 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="divider"></div>
        <h2 class="text-right">TOTAL A PAGAR: ${{ number_format((float) ($invoice->totals['total_pagar'] ?? 0), 2) }}</h2>
        <br>
        <div class="row text-center">¡Gracias por su Compra!</div>
    </div>
    <div class="row">-</div>
</body>

</html>
