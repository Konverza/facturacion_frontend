<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Devolución</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
        }

        .container {
            padding: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #1e40af;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 20px;
            color: #1e40af;
            margin-bottom: 3px;
        }

        .header h2 {
            font-size: 14px;
            color: #444;
            margin-bottom: 6px;
        }

        .header .subtitle {
            font-size: 14px;
            color: #1e3a8a;
            font-weight: bold;
        }

        .header p {
            font-size: 12px;
            color: #666;
        }

        .alert-box {
            background: #eff6ff;
            border-left: 4px solid #1e40af;
            padding: 10px;
            margin: 12px 0;
        }

        .alert-box h3 {
            color: #1e3a8a;
            font-size: 13px;
            margin-bottom: 5px;
        }

        .alert-box p {
            font-size: 11px;
            color: #1e3a8a;
            line-height: 1.4;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 12px;
        }

        .info-row {
            display: table-row;
        }

        .info-cell {
            display: table-cell;
            padding: 5px;
            border: 1px solid #ddd;
            background: #f9fafb;
            font-size: 10px;
        }

        .info-label {
            font-weight: bold;
            width: 30%;
            background: #dbeafe;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            margin-top: 15px;
            margin-bottom: 8px;
            padding-bottom: 3px;
            border-bottom: 2px solid #e5e7eb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        th {
            background: #1e40af;
            color: white;
            padding: 6px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
        }

        td {
            padding: 5px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
        }

        tr:nth-child(even) {
            background: #f9fafb;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .totals {
            margin-top: 12px;
            text-align: right;
        }

        .totals table {
            width: 280px;
            margin-left: auto;
        }

        .totals td {
            font-size: 11px;
            padding: 4px;
        }

        .totals .total-final {
            font-weight: bold;
            font-size: 13px;
            background: #eff6ff;
            border-top: 2px solid #1e40af;
        }

        .footer {
            margin-top: 25px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
        }

        .firma {
            margin-top: 30px;
            text-align: center;
        }

        .firma-linea {
            width: 280px;
            border-top: 2px solid #000;
            margin: 0 auto 8px;
        }

        .firma-texto {
            font-size: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>{{ strtoupper($business_data['nombre']) }}</h1>
            <p style="font-size: 12px; color: #747a85; margin-top: 4px;">{{ $business_data['complemento'] }}</p>
            <p style="font-size: 12px; color: #747a85; margin-bottom: 8px;">Registro de Contribuyente:
                {{ $business_data['nrc'] }} NIT: {{ $business_data['nit'] }}</p>
            <h2>REPORTE DE DEVOLUCIÓN</h2>
            <div class="subtitle">Devolución Masiva de Inventario</div>
            <p style="font-size: 11px; color: #9ca3af;">{{ $traslado->numero_transferencia }}</p>
        </div>

        <!-- Alerta -->
        <div class="alert-box">
            <h3>Devolución Masiva de Inventario</h3>
            <p>
                Este documento registra la devolución completa del inventario desde el punto de venta hacia la sucursal.
                Todos los productos con stock en el punto de venta han sido devueltos.
            </p>
        </div>

        <!-- Información General -->
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell info-label">Fecha Devolución:</div>
                <div class="info-cell">{{ $traslado->created_at->format('d/m/Y H:i') }}</div>
                <div class="info-cell info-label">Requiere Liquidación:</div>
                <div class="info-cell">
                    @if ($traslado->requiere_liquidacion)
                        <strong style="color: #dc2626;">SÍ</strong>
                    @else
                        NO
                    @endif
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">Punto de Venta:</div>
                <div class="info-cell" colspan="3">{{ $traslado->puntoVentaOrigen->nombre }}</div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">Sucursal Destino:</div>
                <div class="info-cell" colspan="3">{{ $traslado->sucursalDestino->nombre }}</div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">Responsable:</div>
                <div class="info-cell" colspan="3">{{ $traslado->user->name }}</div>
            </div>
        </div>

        <!-- Productos -->
        <div class="section-title">PRODUCTOS DEVUELTOS</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">#</th>
                    <th style="width: 15%;">Código</th>
                    <th style="width: 10%;" class="text-center">Unidad</th>
                    <th style="width: 50%;">Descripción</th>
                    <th style="width: 15%;" class="text-center">Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @if ($traslado->esTransferenciaMultiple())
                    @foreach ($traslado->items as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $item->businessProduct->codigo }}</td>
                            <td class="text-center">{{ $unidades_medidas[$item->businessProduct->uniMedida] ?? 'UND' }}
                            </td>
                            <td>{{ $item->businessProduct->descripcion }}</td>
                            <td class="text-center"><strong>{{ number_format($item->cantidad_solicitada, 2) }}</strong>
                            </td>
                        </tr>
                    @endforeach
                @else
                    {{-- Devolución legacy (un solo producto) --}}
                    <tr>
                        <td class="text-center">1</td>
                        <td>{{ $traslado->businessProduct->codigo }}</td>
                        <td class="text-center">{{ $unidades_medidas[$traslado->businessProduct->uniMedida] ?? 'UND' }}
                        </td>
                        <td>{{ $traslado->businessProduct->descripcion }}</td>
                        <td class="text-center"><strong>{{ number_format($traslado->cantidad, 2) }}</strong></td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Totales -->
        <div class="totals">
            <table>
                <tr>
                    <td>Total de Items Devueltos:</td>
                    <td class="text-right">
                        <strong>{{ $traslado->esTransferenciaMultiple() ? $traslado->items->count() : 1 }}</strong>
                    </td>
                </tr>
                <tr class="total-final">
                    <td>Cantidad Total Devuelta:</td>
                    <td class="text-right">
                        <strong>{{ $traslado->esTransferenciaMultiple() ? number_format($traslado->items->sum('cantidad_solicitada'), 2) : number_format($traslado->cantidad, 2) }}</strong>
                    </td>
                </tr>
            </table>
        </div>

        @if ($traslado->requiere_liquidacion && !$traslado->liquidacion_completada)
            <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 12px; margin: 15px 0;">
                <h3 style="color: #92400e; font-size: 13px; margin-bottom: 6px;">Liquidación Pendiente</h3>
                <p style="font-size: 11px; color: #78350f; line-height: 1.4;">
                    Esta devolución requiere un proceso de liquidación donde se debe verificar la cantidad real recibida
                    de cada producto.
                    Se generará un reporte de liquidación por separado.
                </p>
            </div>
        @elseif($traslado->liquidacion_completada)
            <div style="background: #dcfce7; border-left: 4px solid #16a34a; padding: 12px; margin: 15px 0;">
                <h3 style="color: #166534; font-size: 13px; margin-bottom: 6px;">Liquidación Completada</h3>
                <p style="font-size: 11px; color: #14532d; line-height: 1.4;">
                    La liquidación de esta devolución ha sido completada. Consulte el reporte de liquidación para ver el
                    detalle de diferencias.
                </p>
            </div>
        @endif

        @if ($traslado->notas)
            <div class="section-title">OBSERVACIONES</div>
            <div style="padding: 10px; background: #f9fafb; border-left: 4px solid #dc2626; margin-bottom: 20px;">
                {{ $traslado->notas }}
            </div>
        @endif

        <!-- Firmas -->
        <div class="firma">
            <table style="width: 100%; margin-top: 40px;">
                <tr>
                    <td style="width: 50%; text-align: center; padding: 20px;">
                        <div class="firma-linea"></div>
                        <div class="firma-texto">
                            <strong>Devuelto por:</strong><br>
                            {{ $traslado->user->name }}<br>
                            Punto de Venta: {{ $traslado->puntoVentaOrigen->nombre }}<br>
                            Fecha: _______________________
                        </div>
                    </td>
                    <td style="width: 50%; text-align: center; padding: 20px;">
                        <div class="firma-linea"></div>
                        <div class="firma-texto">
                            <strong>Recibido por:</strong><br>
                            _______________________________<br>
                            Sucursal: {{ $traslado->sucursalDestino->nombre }}<br>
                            Fecha: _______________________
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Documento generado el {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>

</html>
