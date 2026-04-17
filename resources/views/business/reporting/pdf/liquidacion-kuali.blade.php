<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Liquidación de Ventas Diarias</title>
    <style>
        @page {
            margin: 12px 8px 6px 8px;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            font-size: 9px;
        }

        h1,
        h2 {
            text-align: center;
            margin: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .info {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
        }

        .info td {
            border: none;
            padding: 3px;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background: #eaeaea;
            font-size: 9px;
        }

        .left {
            text-align: left;
        }

        .totales {
            font-weight: bold;
        }

        .firmas {
            margin-top: 28px;
            width: 100%;
        }

        .firmas td {
            border: none;
            text-align: center;
            padding-top: 28px;
        }

        .linea {
            border-top: 1px solid #000;
            margin-top: 20px;
        }

        .kuali-table {
            table-layout: fixed;
            width: 100%;
        }

        .kuali-table th,
        .kuali-table td {
            font-size: 8px;
            padding: 2px;
            word-break: break-word;
        }

        .kuali-table th {
            font-size: 7.5px;
        }

        .kuali-table .col-doc {
            width: 6%;
        }

        .kuali-table .col-numero {
            width: 11%;
        }

        .kuali-table .col-cliente {
            width: 13%;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>{{ $business->nombre }}</h2>
        <strong>Liquidación de Ventas Diarias</strong>
    </div>

    <table class="info">
        <tr>
            <td><strong>FECHA:</strong> {{ $period_text }}</td>
            <td style="text-align:right;"><strong>NOMBRE DEL VENDEDOR:</strong> {{ $punto_venta_nombre }}</td>
        </tr>
    </table>

    <table class="kuali-table">
        <thead>
            <tr>
                <th rowspan="2" class="col-doc">DOC</th>
                <th rowspan="2" class="col-numero">NUMERO</th>
                <th rowspan="2" class="col-cliente">CLIENTE</th>

                <th colspan="6">CANTIDAD PRODUCTOS</th>
                <th colspan="6">VENTA</th>
                <th colspan="3">FORMA DE PAGO</th>
            </tr>
            <tr>
                <th>GARRAFA</th>
                <th>GARRAFA COMPLETA</th>
                <th>BOTELLA PET KUALI</th>
                <th>BOTELLA PET DON PEDRO</th>
                <th>CAJA PET</th>
                <th>CAJA PET LA CLASICA</th>

                <th>TOTAL GARRAFA</th>
                <th>TOTAL GARRAFA COMPLETA</th>
                <th>TOTAL BOTELLA PET KUALI</th>
                <th>TOTAL BOTELLA PET DON PEDRO</th>
                <th>TOTAL CAJA PET</th>
                <th>TOTAL CAJA PET LA CLASICA</th>

                <th>CREDITO</th>
                <th>CONTADO</th>
                <th>TRANSFERENCIA</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td>{{ $row['doc'] }}</td>
                    <td>{{ $row['numero'] }}</td>
                    <td class="left">{{ $row['cliente'] }}</td>

                    <td>{{ $row['qty_garrafa'] != 0 ? number_format((float) $row['qty_garrafa'], 2) : '' }}</td>
                    <td>{{ $row['qty_garrafa_completa'] != 0 ? number_format((float) $row['qty_garrafa_completa'], 2) : '' }}
                    </td>
                    <td>{{ $row['qty_pet_kuali'] != 0 ? number_format((float) $row['qty_pet_kuali'], 2) : '' }}</td>
                    <td>{{ $row['qty_pet_don_pedro'] != 0 ? number_format((float) $row['qty_pet_don_pedro'], 2) : '' }}
                    </td>
                    <td>{{ $row['qty_caja_pet'] != 0 ? number_format((float) $row['qty_caja_pet'], 2) : '' }}</td>
                    <td>{{ $row['qty_caja_pet_clasica'] != 0 ? number_format((float) $row['qty_caja_pet_clasica'], 2) : '' }}
                    </td>

                    <td>{{ $row['sale_garrafa'] != 0 ? '$' . number_format((float) $row['sale_garrafa'], 2) : '' }}
                    </td>
                    <td>{{ $row['sale_garrafa_completa'] != 0 ? '$' . number_format((float) $row['sale_garrafa_completa'], 2) : '' }}
                    </td>
                    <td>{{ $row['sale_pet_kuali'] != 0 ? '$' . number_format((float) $row['sale_pet_kuali'], 2) : '' }}
                    </td>
                    <td>{{ $row['sale_pet_don_pedro'] != 0 ? '$' . number_format((float) $row['sale_pet_don_pedro'], 2) : '' }}
                    </td>
                    <td>{{ $row['sale_caja_pet'] != 0 ? '$' . number_format((float) $row['sale_caja_pet'], 2) : '' }}
                    </td>
                    <td>{{ $row['sale_caja_pet_clasica'] != 0 ? '$' . number_format((float) $row['sale_caja_pet_clasica'], 2) : '' }}
                    </td>

                    <td>{{ $row['pago_credito'] != 0 ? '$' . number_format((float) $row['pago_credito'], 2) : '' }}
                    </td>
                    <td>{{ $row['pago_contado'] != 0 ? '$' . number_format((float) $row['pago_contado'], 2) : '' }}
                    </td>
                    <td>{{ $row['pago_transferencia'] != 0 ? '$' . number_format((float) $row['pago_transferencia'], 2) : '' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="18">No hay documentos en el rango seleccionado.</td>
                </tr>
            @endforelse

            <tr class="totales">
                <td colspan="3">TOTALES</td>
                <td>{{ number_format((float) ($totals['qty_garrafa'] ?? 0), 2) }}</td>
                <td>{{ number_format((float) ($totals['qty_garrafa_completa'] ?? 0), 2) }}</td>
                <td>{{ number_format((float) ($totals['qty_pet_kuali'] ?? 0), 2) }}</td>
                <td>{{ number_format((float) ($totals['qty_pet_don_pedro'] ?? 0), 2) }}</td>
                <td>{{ number_format((float) ($totals['qty_caja_pet'] ?? 0), 2) }}</td>
                <td>{{ number_format((float) ($totals['qty_caja_pet_clasica'] ?? 0), 2) }}</td>

                <td>${{ number_format((float) ($totals['sale_garrafa'] ?? 0), 2) }}</td>
                <td>${{ number_format((float) ($totals['sale_garrafa_completa'] ?? 0), 2) }}</td>
                <td>${{ number_format((float) ($totals['sale_pet_kuali'] ?? 0), 2) }}</td>
                <td>${{ number_format((float) ($totals['sale_pet_don_pedro'] ?? 0), 2) }}</td>
                <td>${{ number_format((float) ($totals['sale_caja_pet'] ?? 0), 2) }}</td>
                <td>${{ number_format((float) ($totals['sale_caja_pet_clasica'] ?? 0), 2) }}</td>

                <td>${{ number_format((float) ($totals['pago_credito'] ?? 0), 2) }}</td>
                <td>${{ number_format((float) ($totals['pago_contado'] ?? 0), 2) }}</td>
                <td>${{ number_format((float) ($totals['pago_transferencia'] ?? 0), 2) }}</td>
            </tr>
        </tbody>
    </table>

    <br>

    <table style="width:100%; border-collapse:collapse;">
        <tr>
            <td style="border:none; text-align:right; font-weight:bold;">
                TOTAL DE LA VENTA: ${{ number_format((float) ($totals['total_venta'] ?? 0), 2) }}
            </td>
        </tr>
    </table>

    <table class="firmas">
        <tr>
            <td>
                <div class="linea"></div>
                Nombre y firma de quien liquida
            </td>
            <td>
                <div class="linea"></div>
                Nombre y firma de quien recibe
            </td>
        </tr>
    </table>
</body>

</html>
