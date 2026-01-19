<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        @page { margin: 20mm 12mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; margin: 0; }
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
        .totals { margin-top: 12px; text-align: right; }
        .totals table { width: 280px; margin-left: auto; }
        .totals td { font-size: 11px; padding: 4px; }
        .totals .total-final { font-weight: bold; font-size: 13px; background: #eff6ff; border-top: 2px solid #3b82f6; }
        .footer { margin-top: 25px; padding-top: 12px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 9px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ strtoupper($title) }}</h1>
            <p>{{ $business->nombreComercial ?? $business->nombre ?? '' }}</p>
            <p style="font-size: 11px; color: #9ca3af;">{{ $business->nit ?? '' }}</p>
        </div>

        <div class="section-title">DATOS DEL REPORTE</div>
        <div class="info-grid">
            @foreach ($filters as $label => $value)
                <div class="info-row">
                    <div class="info-cell info-label">{{ $label }}:</div>
                    <div class="info-cell" colspan="3">{{ $value }}</div>
                </div>
            @endforeach
        </div>

        @if (!empty($rows))
            <div class="section-title">DETALLE</div>
            <table>
                <thead>
                    <tr>
                        @foreach ($headers as $header)
                            <th>{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr>
                            @foreach ($row as $cell)
                                <td>{{ $cell }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($totals)
                <div class="totals">
                    <table>
                        <tr class="total-final">
                            <td>{{ $totals['label'] ?? 'TOTAL' }}</td>
                            @if (isset($totals['cantidad']))
                                <td class="text-right">{{ number_format($totals['cantidad'], 2, '.', ',') }}</td>
                            @endif
                            @if (isset($totals['total']))
                                <td class="text-right">$ {{ number_format($totals['total'], 2, '.', ',') }}</td>
                            @endif
                        </tr>
                    </table>
                </div>
            @endif
        @endif

        @if (!empty($sections))
            @foreach ($sections as $section)
                @if (!empty($section['rows']))
                    <div class="section-title">{{ $section['title'] }}</div>
                    <table>
                        <thead>
                            <tr>
                                @foreach ($section['headers'] as $header)
                                    <th>{{ $header }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($section['rows'] as $row)
                                <tr>
                                    @foreach ($row as $cell)
                                        <td>{{ $cell }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if (!empty($section['totals']))
                        <div class="totals">
                            <table>
                                <tr class="total-final">
                                    <td>{{ $section['totals']['label'] ?? 'TOTAL' }}</td>
                                    @if (isset($section['totals']['cantidad']))
                                        <td class="text-right">{{ number_format($section['totals']['cantidad'], 2, '.', ',') }}</td>
                                    @endif
                                    @if (isset($section['totals']['total']))
                                        <td class="text-right">$ {{ number_format($section['totals']['total'], 2, '.', ',') }}</td>
                                    @endif
                                </tr>
                            </table>
                        </div>
                    @endif
                @endif
            @endforeach
        @endif

        <div class="footer">
            <p>Documento generado el {{ now()->format('d/m/Y H:i:s') }}</p>
            <p>{{ $business->nombreComercial ?? $business->nombre ?? '' }} - {{ $business->nit ?? '' }}</p>
        </div>
    </div>
</body>
</html>
