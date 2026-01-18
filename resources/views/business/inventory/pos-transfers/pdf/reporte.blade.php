<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Transferencia</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; }
        .container { padding: 15px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #2563eb; padding-bottom: 10px; }
        .header h1 { font-size: 20px; color: #2563eb; margin-bottom: 3px; }
        .header p { font-size: 12px; color: #666; }
        .info-grid { display: table; width: 100%; margin-bottom: 12px; }
        .info-row { display: table-row; }
        .info-cell { display: table-cell; padding: 5px; border: 1px solid #ddd; background: #f9fafb; font-size: 10px; }
        .info-label { font-weight: bold; width: 30%; background: #eff6ff; }
        .section-title { font-size: 14px; font-weight: bold; color: #1f2937; margin-top: 15px; margin-bottom: 8px; padding-bottom: 3px; border-bottom: 2px solid #e5e7eb; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th { background: #3b82f6; color: white; padding: 6px; text-align: left; font-size: 10px; font-weight: bold; }
        td { padding: 5px; border-bottom: 1px solid #e5e7eb; font-size: 10px; }
        tr:nth-child(even) { background: #f9fafb; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 10px; font-size: 9px; font-weight: bold; }
        .badge-completado { background: #dcfce7; color: #166534; }
        .badge-pendiente { background: #fef3c7; color: #92400e; }
        .totals { margin-top: 12px; text-align: right; }
        .totals table { width: 280px; margin-left: auto; }
        .totals td { font-size: 11px; padding: 4px; }
        .totals .total-final { font-weight: bold; font-size: 13px; background: #eff6ff; border-top: 2px solid #3b82f6; }
        .footer { margin-top: 25px; padding-top: 12px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 9px; color: #9ca3af; }
        .firma { margin-top: 30px; text-align: center; }
        .firma-linea { width: 280px; border-top: 2px solid #000; margin: 0 auto 8px; }
        .firma-texto { font-size: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>REPORTE DE TRANSFERENCIA</h1>
            <p>{{ $business->nombreComercial }}</p>
            <p style="font-size: 11px; color: #9ca3af;">{{ $traslado->numero_transferencia }}</p>
        </div>

        <!-- Información General -->
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell info-label">Fecha:</div>
                <div class="info-cell">{{ $traslado->created_at->format('d/m/Y H:i') }}</div>
                <div class="info-cell info-label">Estado:</div>
                <div class="info-cell">
                    <span class="badge badge-{{ $traslado->estado }}">
                        {{ strtoupper($traslado->estado) }}
                    </span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">Tipo:</div>
                <div class="info-cell" colspan="3">
                    @if($traslado->tipo_traslado === 'branch_to_pos')
                        Sucursal -> Punto de Venta
                    @elseif($traslado->tipo_traslado === 'pos_to_branch')
                        Punto de Venta -> Sucursal
                    @else
                        Punto de Venta -> Punto de Venta
                    @endif
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">Origen:</div>
                <div class="info-cell">
                    @if($traslado->sucursalOrigen)
                        {{ $traslado->sucursalOrigen->nombre }}
                    @else
                        {{ $traslado->puntoVentaOrigen->nombre }}
                    @endif
                </div>
                <div class="info-cell info-label">Destino:</div>
                <div class="info-cell">
                    @if($traslado->sucursalDestino)
                        {{ $traslado->sucursalDestino->nombre }}
                    @else
                        {{ $traslado->puntoVentaDestino->nombre }}
                    @endif
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">Usuario:</div>
                <div class="info-cell" colspan="3">{{ $traslado->user->name }}</div>
            </div>
        </div>

        <!-- Productos -->
        <div class="section-title">PRODUCTOS TRANSFERIDOS</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">#</th>
                    <th style="width: 15%;">Código</th>
                    <th style="width: 15%;" class="text-center">Unidad</th>
                    <th style="width: 45%;">Descripción</th>
                    <th style="width: 15%;" class="text-center">Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @if($traslado->esTransferenciaMultiple())
                    @foreach($traslado->items as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $item->businessProduct->codigo }}</td>
                        <td class="text-center">{{ $unidades_medidas[$item->businessProduct->uniMedida] ?? 'UND' }}</td>
                        <td>{{ $item->businessProduct->descripcion }}</td>
                        <td class="text-center"><strong>{{ number_format($item->cantidad_solicitada, 2) }}</strong></td>
                    </tr>
                    @if($item->nota_item)
                    <tr>
                        <td colspan="5" style="padding-left: 60px; font-size: 10px; color: #666; font-style: italic;">
                            Nota: {{ $item->nota_item }}
                        </td>
                    </tr>
                    @endif
                    @endforeach
                @else
                    {{-- Transferencia legacy (un solo producto) --}}
                    <tr>
                        <td class="text-center">1</td>
                        <td>{{ $traslado->businessProduct->codigo }}</td>
                        <td class="text-center">{{ $unidades_medidas[$traslado->businessProduct->uniMedida] ?? 'UND' }}</td>
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
                    <td>Total de Items:</td>
                    <td class="text-right"><strong>{{ $traslado->esTransferenciaMultiple() ? $traslado->items->count() : 1 }}</strong></td>
                </tr>
                <tr class="total-final">
                    <td>Cantidad Total:</td>
                    <td class="text-right"><strong>{{ $traslado->esTransferenciaMultiple() ? number_format($traslado->items->sum('cantidad_solicitada'), 2) : number_format($traslado->cantidad, 2) }}</strong></td>
                </tr>
            </table>
        </div>

        @if($traslado->notas)
        <div class="section-title">OBSERVACIONES</div>
        <div style="padding: 10px; background: #f9fafb; border-left: 4px solid #3b82f6; margin-bottom: 20px;">
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
                            <strong>Entregado por:</strong><br>
                            {{ $traslado->user->name }}<br>
                            Fecha: _______________________
                        </div>
                    </td>
                    <td style="width: 50%; text-align: center; padding: 20px;">
                        <div class="firma-linea"></div>
                        <div class="firma-texto">
                            <strong>Recibido por:</strong><br>
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
            <p>{{ $business->nombreComercial }} - {{ $business->nit ?? '' }}</p>
        </div>
    </div>
</body>
</html>
