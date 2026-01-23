<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Resumen Bolsón {{ $bag->bag_code }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            color: #111;
        }

        h1 {
            font-size: 16px;
            margin: 0 0 1px;
        }

        h2 {
            font-size: 12px;
            margin: 0 0 4px;
            font-weight: normal;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
        }

        th {
            background: #f2f2f2;
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .muted {
            color: #666;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 10px;
        }

        .badge-ok {
            background: #e5f6ed;
            color: #2a7b4f;
        }

        .badge-no {
            background: #fdecec;
            color: #a13a3a;
        }

        .total {
            margin-top: 10px;
            text-align: right;
            font-weight: bold;
        }

        .mb-8 {
            margin-bottom: 32px;
        }

        .footer {
            position: fixed;
            bottom: 0px;
            width: 100%;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>

<body>
    <h1 class="text-center">{{ $business_data['nombre'] }}</h1>
    <h2 class="text-center">{{ $business_data['complemento'] }}</h2>
    <h2 class="text-center">Registro de Contribuyente: {{ $business_data['nrc'] }} NIT: {{ $business_data['nit'] }}</h2>
    <h1 class="text-center mb-8">Resumen de Facturas en Bolsón</h1>
    <h2 class="muted">Código: {{ $bag->bag_code }}</h2>
    <h2 class="muted mb-8">Fecha: {{ $bag->bag_date->format('d/m/Y') }}</h2>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Correlativo</th>
                <th>Cliente</th>
                <th class="text-right">Total</th>
                <th class="text-center">Convertida a DTE</th>
                <th>Código de generación</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bagInvoices as $invoice)
                @php
                    $converted = in_array($invoice->status, ['converted', 'included'], true);
                @endphp
                <tr>
                    <td>{{ $invoice->created_at->format('d/m/Y H:i') }}</td>
                    <td>#{{ $invoice->correlative }}</td>
                    <td>{{ data_get($invoice, 'customer_data.nombre_receptor', 'Consumidor final') }}</td>
                    <td class="text-right">${{ number_format((float) data_get($invoice, 'totals.total_pagar', 0), 2) }}
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $converted ? 'badge-ok' : 'badge-no' }}">
                            {{ $converted ? 'Sí' : 'No' }}
                        </span>
                    </td>
                    <td>{{ $invoice->dte_id ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2 class="total">Total bolsón: ${{ number_format((float) $bagTotal, 2) }}</h2>
    <div class="footer">Reporte generado el: {{ now()->format('d/m/Y H:i') }}</div>
</body>

</html>
