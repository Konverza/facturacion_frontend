<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Liquidación</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; }
        .container { padding: 15px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #059669; padding-bottom: 10px; }
        .header h1 { font-size: 20px; color: #059669; margin-bottom: 3px; }
        .header h2 { font-size: 14px; color: #444; margin-bottom: 6px; }
        .header .subtitle { font-size: 14px; color: #047857; font-weight: bold; }
        .header p { font-size: 12px; color: #666; }
        .status-box { background: #d1fae5; border: 2px solid #059669; padding: 10px; margin: 12px 0; text-align: center; }
        .status-box h3 { color: #065f46; font-size: 14px; font-weight: bold; }
        .info-grid { display: table; width: 100%; margin-bottom: 12px; }
        .info-row { display: table-row; }
        .info-cell { display: table-cell; padding: 5px; border: 1px solid #ddd; background: #f9fafb; font-size: 10px; }
        .info-label { font-weight: bold; width: 30%; background: #d1fae5; }
        .section-title { font-size: 14px; font-weight: bold; color: #1f2937; margin-top: 15px; margin-bottom: 8px; padding-bottom: 3px; border-bottom: 2px solid #e5e7eb; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th { background: #059669; color: white; padding: 6px; text-align: left; font-size: 10px; font-weight: bold; }
        td { padding: 5px; border-bottom: 1px solid #e5e7eb; font-size: 10px; }
        tr:nth-child(even) { background: #f9fafb; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .diferencia-positiva { color: #059669; font-weight: bold; }
        .diferencia-negativa { color: #dc2626; font-weight: bold; }
        .diferencia-cero { color: #6b7280; }
        .resumen-box { background: #f0fdf4; border: 2px solid #86efac; padding: 10px; margin: 12px 0; }
        .resumen-item { display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px dotted #86efac; font-size: 10px; }
        .resumen-item:last-child { border-bottom: none; font-weight: bold; font-size: 11px; }
        .footer { margin-top: 25px; padding-top: 12px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 9px; color: #9ca3af; }
        .firma { margin-top: 30px; text-align: center; }
        .firma-linea { width: 280px; border-top: 2px solid #000; margin: 0 auto 8px; }
        .firma-texto { font-size: 10px; }
        .highlight { background: #fef3c7; padding: 2px 4px; border-radius: 3px; font-weight: bold; }
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
            <h2>REPORTE DE LIQUIDACIÓN</h2>
            <p style="font-size: 11px; color: #9ca3af;">{{ $traslado->numero_transferencia }}</p>
        </div>

        <!-- Status -->
        <div class="status-box">
            <h3>LIQUIDACIÓN COMPLETADA</h3>
            <p style="margin-top: 5px; font-size: 11px;">
                Liquidación procesada el {{ $traslado->updated_at->format('d/m/Y H:i') }}
            </p>
        </div>

        <!-- Información General -->
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell info-label">Fecha Devolución:</div>
                <div class="info-cell">{{ $traslado->created_at->format('d/m/Y H:i') }}</div>
                <div class="info-cell info-label">Fecha Liquidación:</div>
                <div class="info-cell">{{ $traslado->updated_at->format('d/m/Y H:i') }}</div>
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

        <!-- Productos con Liquidación -->
        <div class="section-title">DETALLE DE LIQUIDACIÓN</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 8%;">#</th>
                    <th style="width: 12%;">Código</th>
                    <th style="width: 35%;">Descripción</th>
                    <th style="width: 15%;" class="text-center">Cant. Esperada</th>
                    <th style="width: 15%;" class="text-center">Cant. Real</th>
                    <th style="width: 15%;" class="text-center">Diferencia</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalEsperada = 0;
                    $totalReal = 0;
                    $itemsConDiscrepancia = 0;
                @endphp
                
                @if($traslado->esTransferenciaMultiple())
                    @foreach($traslado->items as $item)
                    @php
                        $totalEsperada += $item->cantidad_solicitada;
                        $totalReal += $item->cantidad_real ?? 0;
                        if($item->diferencia != 0) $itemsConDiscrepancia++;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $item->businessProduct->codigo }}</td>
                        <td>{{ $item->businessProduct->descripcion }}</td>
                        <td class="text-center">{{ number_format($item->cantidad_solicitada, 2) }}</td>
                        <td class="text-center">
                            <span class="highlight">{{ number_format($item->cantidad_real, 2) }}</span>
                        </td>
                        <td class="text-center">
                            @if($item->diferencia > 0)
                                <span class="diferencia-positiva">+{{ number_format($item->diferencia, 2) }}</span>
                            @elseif($item->diferencia < 0)
                                <span class="diferencia-negativa">{{ number_format($item->diferencia, 2) }}</span>
                            @else
                                <span class="diferencia-cero">{{ number_format($item->diferencia, 2) }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                @else
                    {{-- Liquidación legacy (un solo producto) --}}
                    @php
                        // Para transferencias legacy, usar los campos directos
                        $totalEsperada = $traslado->cantidad;
                        $totalReal = $traslado->cantidad; // En legacy no hay diferencia real registrada
                    @endphp
                    <tr>
                        <td class="text-center">1</td>
                        <td>{{ $traslado->businessProduct->codigo }}</td>
                        <td>{{ $traslado->businessProduct->descripcion }}</td>
                        <td class="text-center">{{ number_format($traslado->cantidad, 2) }}</td>
                        <td class="text-center">
                            <span class="highlight">{{ number_format($traslado->cantidad, 2) }}</span>
                        </td>
                        <td class="text-center">
                            <span class="diferencia-cero">0.00</span>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Resumen de Liquidación -->
        <div class="section-title">RESUMEN DE LIQUIDACIÓN</div>
        <div class="resumen-box">
            <div class="resumen-item">
                <span>Total Items:</span>
                <span>{{ $traslado->esTransferenciaMultiple() ? $traslado->items->count() : 1 }}</span>
            </div>
            <div class="resumen-item">
                <span>Cantidad Total Esperada:</span>
                <span>{{ number_format($totalEsperada, 2) }}</span>
            </div>
            <div class="resumen-item">
                <span>Cantidad Total Real:</span>
                <span>{{ number_format($totalReal, 2) }}</span>
            </div>
            <div class="resumen-item">
                <span>Diferencia Total:</span>
                <span class="{{ $totalReal - $totalEsperada > 0 ? 'diferencia-positiva' : ($totalReal - $totalEsperada < 0 ? 'diferencia-negativa' : 'diferencia-cero') }}">
                    {{ number_format($totalReal - $totalEsperada, 2) }}
                </span>
            </div>
            <div class="resumen-item">
                <span>Items con Discrepancia:</span>
                <span style="color: {{ $itemsConDiscrepancia > 0 ? '#dc2626' : '#059669' }};">
                    {{ $itemsConDiscrepancia }}
                </span>
            </div>
        </div>

        @if($itemsConDiscrepancia > 0)
        <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0;">
            <h3 style="color: #92400e; font-size: 14px; margin-bottom: 8px;">Discrepancias Detectadas</h3>
            <p style="font-size: 12px; color: #78350f; line-height: 1.5;">
                Se detectaron {{ $itemsConDiscrepancia }} producto(s) con diferencias entre la cantidad esperada y la cantidad real recibida.
                Revisar las observaciones para más detalles.
            </p>
        </div>
        @else
        <div style="background: #d1fae5; border-left: 4px solid #059669; padding: 15px; margin: 20px 0;">
            <h3 style="color: #065f46; font-size: 14px; margin-bottom: 8px;">Sin Discrepancias</h3>
            <p style="font-size: 12px; color: #047857; line-height: 1.5;">
                Todas las cantidades recibidas coinciden con las cantidades esperadas. La devolución se completó satisfactoriamente.
            </p>
        </div>
        @endif

        @if($traslado->observaciones_liquidacion)
        <div class="section-title">OBSERVACIONES DE LIQUIDACIÓN</div>
        <div style="padding: 10px; background: #f9fafb; border-left: 4px solid #059669; margin-bottom: 20px;">
            {{ $traslado->observaciones_liquidacion }}
        </div>
        @endif

        <!-- Firmas -->
        <div class="firma">
            <table style="width: 100%; margin-top: 40px;">
                <tr>
                    <td style="width: 50%; text-align: center; padding: 20px;">
                        <div class="firma-linea"></div>
                        <div class="firma-texto">
                            <strong>Liquidado por:</strong><br>
                            {{ $traslado->user->name }}<br>
                            Fecha: {{ $traslado->updated_at->format('d/m/Y') }}
                        </div>
                    </td>
                    <td style="width: 50%; text-align: center; padding: 20px;">
                        <div class="firma-linea"></div>
                        <div class="firma-texto">
                            <strong>Autorizado por:</strong><br>
                            _______________________________<br>
                            Fecha: _______________________
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Documento generado el {{ now()->format('d/m/Y H:i:s') }}</p>
            <p style="margin-top: 5px; font-size: 9px;">
                Este documento certifica la liquidación del inventario devuelto
            </p>
        </div>
    </div>
</body>
</html>
