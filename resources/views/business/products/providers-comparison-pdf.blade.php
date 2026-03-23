<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Comparativa proveedores</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        .header { margin-bottom: 14px; }
        .title { font-size: 18px; font-weight: bold; margin-bottom: 4px; }
        .subtitle { color: #4b5563; }
        .block { margin-top: 14px; }
        .product-title { font-size: 14px; font-weight: bold; margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 6px; }
        th { background: #f3f4f6; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .muted { color: #6b7280; }
        .good { color: #047857; font-weight: bold; }
        .warn { color: #b45309; font-weight: bold; }
        .empty { padding: 16px; border: 1px solid #d1d5db; text-align: center; color: #6b7280; }
        .summary { margin-top: 16px; width: 50%; margin-left: auto; }
        .summary td { border: none; padding: 4px 0; }
    </style>
</head>

<body>
    @php
        $rows = collect();
        $overrides = $product->priceVariantOverrides->keyBy('price_variant_id');

        foreach ($product->costVariants as $costVariant) {
            $salePrice = (float) $product->precioUni;
            $variantName = 'Precio base';

            if ($costVariant->price_variant_id) {
                $variantName = $costVariant->priceVariant?->name ?? 'Variante';
                $override = $overrides->get($costVariant->price_variant_id);

                if ($override && $override->price_with_iva !== null) {
                    $salePrice = (float) $override->price_with_iva;
                } elseif ($costVariant->priceVariant && $costVariant->priceVariant->price_with_iva !== null) {
                    $salePrice = (float) $costVariant->priceVariant->price_with_iva;
                }
            }

            $cost = (float) $costVariant->costo_final;
            $marginAmount = $salePrice - $cost;
            $marginPercent = $cost > 0 ? ($marginAmount / $cost) * 100 : null;

            $rows->push([
                'supplier' => $costVariant->nombre_proveedor,
                'variant_name' => $variantName,
                'sale_price' => $salePrice,
                'cost' => $cost,
                'margin_amount' => $marginAmount,
                'margin_percent' => $marginPercent,
            ]);
        }

        $bestByMargin = $rows->sortByDesc('margin_amount')->first();
        $bestByCost = $rows->sortBy('cost')->first();
        $recommended = $bestByMargin;
        $recommendedReason = 'Mayor margen esperado';

        if (((float) ($bestByMargin['margin_amount'] ?? 0)) <= 0) {
            $recommended = $bestByCost;
            $recommendedReason = 'Sin margen positivo, se prioriza menor costo';
        }
    @endphp

    <div class="header">
        <div class="title">Comparativa de precios entre proveedores y margen</div>
        <div>{{ $business->name ?? 'Negocio' }}</div>
        <div class="subtitle">Generado: {{ optional($generatedAt)->format('d/m/Y H:i') }}</div>
        <div class="subtitle">Producto: {{ $product->codigo ? $product->codigo . ' - ' : '' }}{{ $product->descripcion }}</div>
    </div>

    @if ($rows->isNotEmpty())
        <div class="block">
            <div class="product-title">
                {{ $product->codigo ? $product->codigo . ' - ' : '' }}{{ $product->descripcion }}
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Proveedor</th>
                        <th>Variante precio aplicada</th>
                        <th class="text-right">Costo proveedor</th>
                        <th class="text-right">Precio venta aplicado</th>
                        <th class="text-right">Margen $</th>
                        <th class="text-right">Margen %</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr>
                            <td>{{ $row['supplier'] }}</td>
                            <td>{{ $row['variant_name'] }}</td>
                            <td class="text-right">${{ number_format((float) $row['cost'], 2) }}</td>
                            <td class="text-right">${{ number_format((float) $row['sale_price'], 2) }}</td>
                            <td class="text-right">${{ number_format((float) $row['margin_amount'], 2) }}</td>
                            <td class="text-right">
                                @if (!is_null($row['margin_percent']))
                                    {{ number_format((float) $row['margin_percent'], 2) }}%
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="muted" style="margin-top: 6px;">
                Mejor por margen: <span class="good">{{ $bestByMargin['supplier'] ?? 'N/A' }}</span>
                ( ${{ number_format((float) ($bestByMargin['margin_amount'] ?? 0), 2) }} )
                | Menor costo: <span class="warn">{{ $bestByCost['supplier'] ?? 'N/A' }}</span>
                ( ${{ number_format((float) ($bestByCost['cost'] ?? 0), 2) }} )
            </div>
        </div>
    @else
        <div class="empty">
            Este producto no tiene costos por proveedor para comparar.
        </div>
    @endif

    @if ($rows->isNotEmpty())
        <div class="block">
            <div class="product-title">Proveedor recomendado</div>
            <table>
                <thead>
                    <tr>
                        <th>Proveedor recomendado</th>
                        <th class="text-right">Costo</th>
                        <th class="text-right">Precio venta</th>
                        <th class="text-right">Margen $</th>
                        <th>Criterio</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $recommended['supplier'] ?? 'N/A' }}</td>
                        <td class="text-right">${{ number_format((float) ($recommended['cost'] ?? 0), 2) }}</td>
                        <td class="text-right">${{ number_format((float) ($recommended['sale_price'] ?? 0), 2) }}</td>
                        <td class="text-right">${{ number_format((float) ($recommended['margin_amount'] ?? 0), 2) }}</td>
                        <td>{{ $recommendedReason }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endif

    <table class="summary">
        <tr>
            <td><b>Filas comparadas:</b></td>
            <td class="text-right">{{ $rows->count() }}</td>
        </tr>
        <tr>
            <td><b>Mejor margen potencial:</b></td>
            <td class="text-right">${{ number_format((float) ($bestByMargin['margin_amount'] ?? 0), 2) }}</td>
        </tr>
    </table>
</body>

</html>
