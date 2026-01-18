<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Kardex</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 12px;
            color: #666;
            font-weight: normal;
        }

        .info-section {
            margin-bottom: 15px;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 120px;
            padding: 3px 5px;
            background-color: #f0f0f0;
        }

        .info-value {
            display: table-cell;
            padding: 3px 5px;
            border-bottom: 1px solid #ddd;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background-color: #333;
            color: white;
            padding: 6px 4px;
            text-align: center;
            font-size: 8px;
            font-weight: bold;
        }

        td {
            padding: 5px 4px;
            border: 1px solid #ddd;
            text-align: center;
            font-size: 8px;
        }

        td.left {
            text-align: left;
        }

        td.right {
            text-align: right;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .saldo-inicial {
            background-color: #e3f2fd;
            font-weight: bold;
        }

        .totales {
            background-color: #f5f5f5;
            font-weight: bold;
            border-top: 2px solid #333;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .badge-entrada {
            background-color: #4caf50;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
        }

        .badge-salida {
            background-color: #f44336;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>REPORTE KARDEX DE INVENTARIO</h1>
        <h2>{{ $business->nombre_de_la_empresa }}</h2>
    </div>

    <!-- Información del Reporte -->
    <div class="info-section">
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Producto:</div>
                <div class="info-value">{{ $producto->codigo }} - {{ $producto->descripcion }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Unidad de Medida:</div>
                <div class="info-value">{{ $producto->uniMedida }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Período:</div>
                <div class="info-value">
                    Del {{ $fechaInicio->format('d/m/Y') }} al {{ $fechaFin->format('d/m/Y') }}
                </div>
            </div>
            @if($sucursal)
                <div class="info-row">
                    <div class="info-label">Sucursal:</div>
                    <div class="info-value">{{ $sucursal->nombre }}</div>
                </div>
            @endif
            @if($puntoVenta)
                <div class="info-row">
                    <div class="info-label">Punto de Venta:</div>
                    <div class="info-value">{{ $puntoVenta->nombre }}</div>
                </div>
            @endif
            <div class="info-row">
                <div class="info-label">Fecha de Emisión:</div>
                <div class="info-value">{{ now()->format('d/m/Y H:i:s') }}</div>
            </div>
        </div>
    </div>

    <!-- Tabla de Movimientos -->
    <table>
        <thead>
            <tr>
                <th style="width: 8%;">Fecha</th>
                <th style="width: 12%;">Documento</th>
                <th style="width: 15%;">Descripción</th>
                <th style="width: 10%;">Sucursal</th>
                <th style="width: 10%;">Punto Venta</th>
                <th style="width: 6%;">Entrada</th>
                <th style="width: 8%;">Valor Ent.</th>
                <th style="width: 6%;">Salida</th>
                <th style="width: 8%;">Valor Sal.</th>
                <th style="width: 6%;">Saldo</th>
                <th style="width: 8%;">Valor Saldo</th>
            </tr>
        </thead>
        <tbody>
            <!-- Saldo Inicial -->
            <tr class="saldo-inicial">
                <td colspan="5" class="left"><strong>SALDO INICIAL</strong></td>
                <td>-</td>
                <td class="right">-</td>
                <td>-</td>
                <td class="right">-</td>
                <td><strong>{{ number_format($saldoInicial, 2) }}</strong></td>
                <td class="right"><strong>${{ number_format($saldoInicial * $producto->precioUni, 2) }}</strong></td>
            </tr>

            <!-- Movimientos -->
            @php
                $totalEntradas = 0;
                $totalSalidas = 0;
                $totalValorEntradas = 0;
                $totalValorSalidas = 0;
            @endphp

            @forelse($kardex as $item)
                @php
                    $totalEntradas += $item['entrada'];
                    $totalSalidas += $item['salida'];
                    $totalValorEntradas += $item['valor_entrada'];
                    $totalValorSalidas += $item['valor_salida'];
                @endphp
                <tr>
                    <td>{{ $item['fecha']->format('d/m/Y') }}</td>
                    <td class="left">{{ $item['documento'] }}</td>
                    <td class="left">{{ Str::limit($item['descripcion'], 30) }}</td>
                    <td class="left">{{ Str::limit($item['sucursal'], 15) }}</td>
                    <td class="left">{{ Str::limit($item['punto_venta'], 15) }}</td>
                    <td>{{ $item['entrada'] > 0 ? number_format($item['entrada'], 2) : '-' }}</td>
                    <td class="right">{{ $item['entrada'] > 0 ? '$' . number_format($item['valor_entrada'], 2) : '-' }}</td>
                    <td>{{ $item['salida'] > 0 ? number_format($item['salida'], 2) : '-' }}</td>
                    <td class="right">{{ $item['salida'] > 0 ? '$' . number_format($item['valor_salida'], 2) : '-' }}</td>
                    <td><strong>{{ number_format($item['saldo'], 2) }}</strong></td>
                    <td class="right"><strong>${{ number_format($item['valor_saldo'], 2) }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" style="text-align: center; padding: 20px; color: #999;">
                        No se encontraron movimientos en el período seleccionado
                    </td>
                </tr>
            @endforelse

            <!-- Totales -->
            @if(count($kardex) > 0)
                <tr class="totales">
                    <td colspan="5" class="right"><strong>TOTALES:</strong></td>
                    <td><strong>{{ number_format($totalEntradas, 2) }}</strong></td>
                    <td class="right"><strong>${{ number_format($totalValorEntradas, 2) }}</strong></td>
                    <td><strong>{{ number_format($totalSalidas, 2) }}</strong></td>
                    <td class="right"><strong>${{ number_format($totalValorSalidas, 2) }}</strong></td>
                    <td colspan="2"></td>
                </tr>
                <tr class="totales">
                    <td colspan="9" class="right"><strong>SALDO FINAL:</strong></td>
                    <td><strong>{{ number_format(end($kardex)['saldo'], 2) }}</strong></td>
                    <td class="right"><strong>${{ number_format(end($kardex)['valor_saldo'], 2) }}</strong></td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>{{ $business->nombre_de_la_empresa }} - NIT: {{ $business->nit }}</p>
        <p>Generado el {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
