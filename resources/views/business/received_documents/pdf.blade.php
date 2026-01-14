<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DTE {{ $codGeneracion }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            line-height: 1.5;
            color: #000;
            padding: 1.5cm 2cm;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #000;
        }
        .header h1 {
            font-size: 16px;
            color: #000;
            margin-bottom: 5px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .header p {
            font-size: 10px;
            color: #333;
        }
        .section {
            margin-bottom: 18px;
            page-break-inside: avoid;
        }
        .section-title {
            background-color: transparent;
            color: #000;
            padding: 7px 0;
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #000;
        }
        .grid {
            display: table;
            width: 100%;
            margin-bottom: 12px;
        }
        .grid-row {
            display: table-row;
        }
        .grid-col {
            display: table-cell;
            padding: 6px 8px;
            vertical-align: top;
            width: 33.33%;
            border-bottom: 1px solid #e5e5e5;
        }
        .grid-col-2 {
            width: 50%;
        }
        .field-label {
            font-size: 7px;
            color: #555;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 3px;
            letter-spacing: 0.3px;
        }
        .field-value {
            font-size: 9px;
            color: #000;
            word-wrap: break-word;
            line-height: 1.4;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 8px;
        }
        table th {
            background-color: #e5e5e5;
            padding: 6px 4px;
            text-align: center;
            font-size: 8px;
            font-weight: bold;
            border: 1px solid #999;
            color: #000;
        }
        table td {
            padding: 5px 4px;
            border: 1px solid #ccc;
            text-align: left;
            color: #000;
        }
        table td.text-right {
            text-align: right;
        }
        table td.text-center {
            text-align: center;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border: 1px solid #000;
            font-size: 8px;
            font-weight: bold;
        }
        .status-success {
            border: 2px solid #000;
            color: #000;
        }
        .status-error {
            border: 2px solid #000;
            background-color: #e5e5e5;
            color: #000;
        }
        .status-warning {
            border: 2px solid #000;
            background-color: #f5f5f5;
            color: #000;
        }
        .total-box {
            background-color: #f5f5f5;
            border: 2px solid #000;
            padding: 12px 15px;
            margin: 12px 0;
        }
        .total-box .label {
            font-size: 9px;
            color: #333;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .total-box .value {
            font-size: 16px;
            color: #000;
            font-weight: bold;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    @php
        $documento = json_decode($dte['documento']);
        $types = [
            '01' => 'Factura Consumidor Final',
            '03' => 'Comprobante de crédito fiscal',
            '04' => 'Nota de Remisión',
            '05' => 'Nota de crédito',
            '06' => 'Nota de débito',
            '07' => 'Comprobante de retención',
            '11' => 'Factura de exportación',
            '14' => 'Factura de sujeto excluido',
            '15' => 'Comprobante de Donación',
        ];

        $emisor = isset($documento->emisor)
            ? $documento->emisor
            : (isset($documento->donante)
                ? $documento->donante
                : null);
        $tipoEmisor = isset($documento->donante) ? 'Donante' : 'Emisor';

        $receptor = isset($documento->receptor)
            ? $documento->receptor
            : (isset($documento->sujetoExcluido)
                ? $documento->sujetoExcluido
                : (isset($documento->donatario)
                    ? $documento->donatario
                    : null));
        $tipoReceptor = isset($documento->sujetoExcluido)
            ? 'Sujeto Excluido'
            : (isset($documento->donatario)
                ? 'Donatario'
                : 'Receptor');
        
        $tipoDte = $documento->identificacion->tipoDte;
    @endphp

    <div class="header">
        <h1>Detalle del DTE Recibido</h1>
        <p style="font-size: 12px; color: #666; margin-top: 8px; font-style: italic;">
            Esta es una representación informativa del JSON recibido, únicamente para consulta interna.
            No sustituye la representación gráfica oficial que su proveedor debe entregarle.
        </p>
        <p>{{ $types[$dte['tipo_dte']] ?? 'Documento Tributario Electrónico' }}</p>
    </div>

    {{-- Identificación --}}
    <div class="section">
        <div class="section-title">IDENTIFICACIÓN</div>
        <div class="grid">
            <div class="grid-row">
                <div class="grid-col">
                    <div class="field-label">Código de generación</div>
                    <div class="field-value">{{ $codGeneracion }}</div>
                </div>
                <div class="grid-col">
                    <div class="field-label">Número de Control</div>
                    <div class="field-value">{{ $documento->identificacion->numeroControl }}</div>
                </div>
                <div class="grid-col">
                    <div class="field-label">Fecha de Emisión</div>
                    <div class="field-value">
                        {{ \Carbon\Carbon::parse($documento->identificacion->fecEmi)->format('d/m/Y') }}
                        {{ $documento->identificacion->horEmi }}
                    </div>
                </div>
            </div>
            <div class="grid-row">
                <div class="grid-col">
                    <div class="field-label">Estado</div>
                    <div class="field-value">
                        @if (in_array($dte['estado'], ['PROCESADO', 'VALIDADO', 'OBSERVADO']))
                            Procesado
                        @elseif($dte['estado'] === 'RECHAZADO')
                            Rechazado
                        @else
                            {{ $dte['estado'] }}
                        @endif
                    </div>
                </div>
                <div class="grid-col">
                    <div class="field-label">Tipo de DTE</div>
                    <div class="field-value">{{ $types[$dte['tipo_dte']] }}</div>
                </div>
                <div class="grid-col">
                    <div class="field-label">Sello de Recepción</div>
                    <div class="field-value" style="font-size: 7px;">{{ $dte['selloRecibido'] }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Emisor y Receptor --}}
    <div class="grid">
        <div class="grid-row">
            <div class="grid-col grid-col-2">
                <div class="section">
                    <div class="section-title">{{ $tipoEmisor }}</div>
                    <div class="field-label">Nombre</div>
                    <div class="field-value" style="margin-bottom: 5px;">{{ $emisor->nombre }}</div>
                    
                    @if ($emisor->nombreComercial ?? null)
                        <div class="field-label">Nombre Comercial</div>
                        <div class="field-value" style="margin-bottom: 5px;">{{ $emisor->nombreComercial }}</div>
                    @endif
                    
                    <div class="field-label">Identificación</div>
                    <div class="field-value" style="margin-bottom: 5px;">
                        @if (isset($emisor->numDocumento) && $emisor->numDocumento)
                            {{ $catalogos['tipos_documentos'][$emisor->tipoDocumento] ?? 'N/A' }} - {{ $emisor->numDocumento }}
                        @elseif(isset($emisor->nit))
                            NIT: {{ $emisor->nit }}
                        @endif
                    </div>
                    
                    @if (isset($emisor->nrc))
                        <div class="field-label">NRC</div>
                        <div class="field-value">{{ $emisor->nrc }}</div>
                    @endif
                </div>
            </div>
            
            @if ($receptor)
                <div class="grid-col grid-col-2">
                    <div class="section">
                        <div class="section-title">{{ $tipoReceptor }}</div>
                        <div class="field-label">Nombre</div>
                        <div class="field-value" style="margin-bottom: 5px;">{{ $receptor->nombre }}</div>
                        
                        <div class="field-label">Identificación</div>
                        <div class="field-value" style="margin-bottom: 5px;">
                            @if (isset($receptor->numDocumento) && $receptor->numDocumento)
                                {{ $catalogos['tipos_documentos'][$receptor->tipoDocumento] ?? 'N/A' }} - {{ $receptor->numDocumento }}
                            @elseif(isset($receptor->nit))
                                NIT: {{ $receptor->nit }}
                            @endif
                        </div>
                        
                        @if (isset($receptor->nrc) && $receptor->nrc)
                            <div class="field-label">NRC</div>
                            <div class="field-value">{{ $receptor->nrc }}</div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Cuerpo del Documento --}}
    @php
        $cuerpoDocumento = $documento->cuerpoDocumento ?? [];
    @endphp

    @if ($tipoDte !== '09' && !empty($cuerpoDocumento))
        <div class="section">
            <div class="section-title">CUERPO DEL DOCUMENTO</div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        @if (in_array($tipoDte, ['01', '03', '04', '05', '06', '11', '14', '15']))
                            <th>Código</th>
                        @endif
                        <th>Descripción</th>
                        @if (in_array($tipoDte, ['01', '03', '04', '05', '06', '11', '14', '15']))
                            <th>Cant.</th>
                        @endif
                        @if (in_array($tipoDte, ['01', '03', '04', '05', '06', '11', '14']))
                            <th>P. Unit.</th>
                            <th>Desc.</th>
                        @endif
                        @if (in_array($tipoDte, ['01', '03', '04', '05', '06', '08', '11']))
                            <th>V. Gravadas</th>
                        @endif
                        @if (in_array($tipoDte, ['01', '03', '04', '05', '06', '08']))
                            <th>V. Exentas</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($cuerpoDocumento as $item)
                        <tr>
                            <td class="text-center">{{ $item->numItem ?? $loop->iteration }}</td>
                            @if (in_array($tipoDte, ['01', '03', '04', '05', '06', '11', '14', '15']))
                                <td>{{ $item->codigo ?? '-' }}</td>
                            @endif
                            <td>{{ $item->descripcion ?? '-' }}</td>
                            @if (in_array($tipoDte, ['01', '03', '04', '05', '06', '11', '14', '15']))
                                <td class="text-right">{{ number_format($item->cantidad ?? 0, 2) }}</td>
                            @endif
                            @if (in_array($tipoDte, ['01', '03', '04', '05', '06', '11', '14']))
                                <td class="text-right">${{ number_format($item->precioUni ?? 0, 2) }}</td>
                                <td class="text-right">${{ number_format($item->montoDescu ?? 0, 2) }}</td>
                            @endif
                            @if (in_array($tipoDte, ['01', '03', '04', '05', '06', '08', '11']))
                                <td class="text-right">${{ number_format($item->ventaGravada ?? 0, 2) }}</td>
                            @endif
                            @if (in_array($tipoDte, ['01', '03', '04', '05', '06', '08']))
                                <td class="text-right">${{ number_format($item->ventaExenta ?? 0, 2) }}</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Resumen --}}
    @if ($tipoDte !== '09')
        @php
            $resumen = $documento->resumen ?? null;
        @endphp

        @if ($resumen)
            <div class="section">
                <div class="section-title">RESUMEN</div>
                
                @if (in_array($tipoDte, ['01', '03']))
                    <div class="total-box">
                        <div class="label">Total a Pagar</div>
                        <div class="value">${{ number_format($resumen->totalPagar ?? 0, 2) }}</div>
                    </div>
                    <div class="field-label">Total en Letras</div>
                    <div class="field-value">{{ $resumen->totalLetras ?? '' }}</div>
                @elseif (in_array($tipoDte, ['04', '05', '06']))
                    <div class="total-box">
                        <div class="label">Monto Total de la Operación</div>
                        <div class="value">${{ number_format($resumen->montoTotalOperacion ?? 0, 2) }}</div>
                    </div>
                    <div class="field-label">Total en Letras</div>
                    <div class="field-value">{{ $resumen->totalLetras ?? '' }}</div>
                @endif

                {{-- Pagos --}}
                @if (isset($resumen->pagos) && is_array($resumen->pagos) && count($resumen->pagos) > 0)
                    <div style="margin-top: 10px;">
                        <div class="field-label">Formas de Pago</div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Monto</th>
                                    <th>Referencia</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($resumen->pagos as $pago)
                                    <tr>
                                        <td>{{ $pago->codigo ?? '-' }}</td>
                                        <td class="text-right">${{ number_format($pago->montoPago ?? 0, 2) }}</td>
                                        <td>{{ $pago->referencia ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif
    @endif

</body>
</html>
