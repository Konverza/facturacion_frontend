<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; }
        h1 { font-size: 16px; margin: 0 0 8px; }
        h2 { font-size: 12px; margin: 12px 0 6px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 5px; text-align: left; }
        th { background: #f3f4f6; }
        .right { text-align: right; }
        .muted { color: #6b7280; }
    </style>
</head>

<body>
    <h1>Proyecto - Etapa 2 (Costeo)</h1>
    <div class="muted">Proyecto: {{ $project->name }} | Fecha: {{ now()->format('d/m/Y H:i') }}</div>

    <h2>Parámetros</h2>
    <table>
        <tbody>
            <tr>
                <th>% Ganancia</th>
                <td>{{ number_format((float) ($costing['profit_margin_percent'] ?? 0), 2) }}%</td>
                <th>Mano de obra</th>
                <td>{{ $costing['labor_concept'] ?? 'Mano de obra' }}</td>
                <th>Costo mano de obra</th>
                <td class="right">${{ number_format((float) ($costing['labor_cost'] ?? 0), 4) }}</td>
            </tr>
        </tbody>
    </table>

    <h2>Detalle de costeo</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Cantidad</th>
                <th>U.M.</th>
                <th>Descripción</th>
                <th>Código</th>
                <th>Costo Individual</th>
                <th>Costo Total</th>
                <th>Precio Unitario</th>
                <th>% Descuento</th>
                <th>P. Unit c/ desc.</th>
                <th>Precio Total</th>
                <th>Ganancia</th>
                <th>Proveedor</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($costing['items'] ?? [] as $row)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ number_format((float) $row['cantidad'], 2) }}</td>
                    <td>{{ $row['unidad_medida'] ?: '-' }}</td>
                    <td>{{ $row['descripcion'] }}</td>
                    <td>{{ $row['codigo'] ?: '-' }}</td>
                    <td class="right">${{ number_format((float) $row['cost_individual'], 4) }}</td>
                    <td class="right">${{ number_format((float) $row['cost_total'], 4) }}</td>
                    <td class="right">${{ number_format((float) $row['price_unit'], 4) }}</td>
                    <td class="right">{{ number_format((float) $row['discount_percent'], 2) }}%</td>
                    <td class="right">${{ number_format((float) $row['price_unit_with_discount'], 4) }}</td>
                    <td class="right">${{ number_format((float) $row['price_total'], 4) }}</td>
                    <td class="right">${{ number_format((float) $row['gain'], 4) }}</td>
                    <td>{{ $row['provider'] ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="right">No hay items.</td>
                </tr>
            @endforelse

            <tr>
                <td>{{ count($costing['items'] ?? []) + 1 }}</td>
                <td>1.00</td>
                <td>-</td>
                <td>{{ $costing['labor_concept'] ?? 'Mano de obra' }}</td>
                <td>MANO-OBRA</td>
                <td class="right">${{ number_format((float) ($costing['labor_cost'] ?? 0), 4) }}</td>
                <td class="right">${{ number_format((float) ($costing['labor_cost'] ?? 0), 4) }}</td>
                <td class="right">${{ number_format((float) ($costing['labor_cost'] ?? 0), 4) }}</td>
                <td class="right">0.00%</td>
                <td class="right">${{ number_format((float) ($costing['labor_cost'] ?? 0), 4) }}</td>
                <td class="right">${{ number_format((float) ($costing['labor_cost'] ?? 0), 4) }}</td>
                <td class="right">$0.0000</td>
                <td>-</td>
            </tr>
        </tbody>
    </table>

    <h2>Resumen</h2>
    <table>
        <tbody>
            <tr>
                <th>Inversión materiales (Sin IVA)</th>
                <td class="right">${{ number_format((float) ($costing['summary']['materials_cost_without_iva'] ?? 0), 4) }}</td>
            </tr>
            <tr>
                <th>Inversión materiales (Con IVA)</th>
                <td class="right">${{ number_format((float) ($costing['summary']['materials_cost_with_iva'] ?? 0), 4) }}</td>
            </tr>
            <tr>
                <th>Venta materiales (Sin IVA)</th>
                <td class="right">${{ number_format((float) ($costing['summary']['materials_sale_without_iva'] ?? 0), 4) }}</td>
            </tr>
            <tr>
                <th>Venta materiales (Con IVA)</th>
                <td class="right">${{ number_format((float) ($costing['summary']['materials_sale_with_iva'] ?? 0), 4) }}</td>
            </tr>
            <tr>
                <th>Ganancia (Sin IVA)</th>
                <td class="right">${{ number_format((float) ($costing['summary']['gain_without_iva'] ?? 0), 4) }}</td>
            </tr>
            <tr>
                <th>Anticipo ({{ number_format((float) ($costing['summary']['advance_percent'] ?? 0), 2) }}%)</th>
                <td class="right">${{ number_format((float) ($costing['summary']['advance_amount'] ?? 0), 4) }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>
