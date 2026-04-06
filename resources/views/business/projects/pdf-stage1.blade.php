<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        h1 { font-size: 18px; margin: 0 0 8px; }
        h2 { font-size: 13px; margin: 14px 0 6px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 6px; text-align: left; }
        th { background: #f3f4f6; }
        .right { text-align: right; }
        .muted { color: #6b7280; }
    </style>
</head>

<body>
    @php
        $items = $comparison['items'] ?? [];
        $providerNames = $comparison['provider_names'] ?? [];
    @endphp

    <h1>Proyecto - Etapa 1 (Comparación)</h1>
    <div class="muted">Proyecto: {{ $project->name }} | Fecha: {{ now()->format('d/m/Y H:i') }}</div>

    <h2>Detalle de comparación</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Cantidad</th>
                <th>Unidad</th>
                <th>Material</th>
                @foreach ($providerNames as $providerName)
                    <th>{{ $providerName }}</th>
                @endforeach
                <th>Precio más bajo</th>
                <th>Proveedor más bajo</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ number_format((float) ($item['cantidad'] ?? 0), 2) }}</td>
                    <td>{{ $item['unidad_medida'] ?? '-' }}</td>
                    <td>{{ $item['descripcion'] ?? '-' }}</td>
                    @foreach ($providerNames as $providerName)
                        @php
                            $supplierMap = $item['supplier_map'] ?? [];
                            $cost = $supplierMap[$providerName] ?? null;
                        @endphp
                        <td class="right">{{ $cost !== null ? '$' . number_format((float) $cost, 4) : '-' }}</td>
                    @endforeach
                    <td class="right">${{ number_format((float) ($item['best_unit_cost'] ?? 0), 4) }}</td>
                    <td>{{ $item['best_supplier'] ?? 'Sin proveedor' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 6 + count($providerNames) }}" class="right">No hay items.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Resumen</h2>
    <table>
        <tbody>
            <tr>
                <th>Costo total con mejor proveedor</th>
                <td class="right">${{ number_format((float) ($comparison['total_best_cost'] ?? 0), 4) }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>
