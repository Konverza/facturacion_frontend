<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Rentabilidad Cotizacion {{ $quotation->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        .header { width: 100%; margin-bottom: 16px; }
        .header td { vertical-align: top; }
        .header, .header td, .header th { border: none !important; }
        .logo { max-width: 140px; max-height: 80px; width: auto; height: auto; display: block; }
        .title { font-size: 18px; font-weight: bold; margin-bottom: 6px; }
        .muted { color: #6b7280; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 6px; }
        th { background: #f3f4f6; font-weight: bold; }
        .summary { margin-top: 10px; width: 50%; margin-left: auto; }
        .summary td { border: none; padding: 4px 0; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-danger { color: #b91c1c; }
        .text-success { color: #047857; }
    </style>
</head>

<body>
    @php
        $company = $companyData['data'] ?? $companyData;
        $products = $content['products'] ?? [];
        $validityDays = (int) ($meta['vigencia_dias'] ?? 15);
        $reportTotalCosts = 0.0;
        $reportTotalSalesBeforeDiscounts = 0.0;
        $reportItemDiscounts = 0.0;
    @endphp

    <table class="header">
        <tr>
            <td style="width: 22%;">
                @if ($logo)
                    <img src="{{ $logo }}" alt="Logo" class="logo" width="180" style="height:auto; width:auto; max-width:180px; max-height:120px;">
                @endif
            </td>
            <td style="width: 48%; text-align: center;">
                <div class="title">Reporte de rentabilidad</div>
                <div>{{ $company['nombre'] ?? $business->name ?? 'N/A' }}</div>
                <div><b>NIT:</b> {{ $company['nit'] ?? $business->nit ?? 'N/A' }}</div>
                <div><b>NRC:</b> {{ $company['nrc'] ?? ($business->nrc ?? 'N/A') }}</div>
                <div><b>Correo:</b> {{ $company['correo'] ?? ($business->email ?? 'N/A') }}</div>
            </td>
            <td style="width: 30%; text-align: right;">
                <div><b>Cotizacion:</b> #{{ $quotation->id }}</div>
                <div><b>Emitida:</b> {{ $quotation->updated_at?->format('d/m/Y') }}</div>
                <div><b>Vigencia:</b> {{ $validityDays }} dias</div>
                <div class="muted">Valida hasta {{ optional($quotation->updated_at)->addDays($validityDays)?->format('d/m/Y') }}</div>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Cant.</th>
                <th>Codigo</th>
                <th>Descripcion</th>
                <th>Precio venta seleccionado</th>
                <th>Precio compra seleccionado</th>
                <th>Ganancia</th>
                <th>Total linea</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $idx => $item)
                @php
                    $qty = (float) ($item['cantidad'] ?? 0);
                    $salePrice = (float) ($item['precio_unitario'] ?? ($item['precio'] ?? ($item['precio_sin_tributos'] ?? 0)));
                    $supplierCost = (float) ($item['supplier_cost'] ?? 0);
                    $discount = (float) ($item['descuento'] ?? 0);
                    $lineGrossTotal = $qty * $salePrice;
                    $lineSalesTotal = ($qty * $salePrice) - $discount;
                    $lineCostsTotal = $qty * $supplierCost;
                    $reportTotalSalesBeforeDiscounts += $lineGrossTotal;
                    $reportItemDiscounts += $discount;
                    $reportTotalCosts += $lineCostsTotal;
                    $lineProfit = $lineSalesTotal - $lineCostsTotal;
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-right">{{ number_format($qty, 2) }}</td>
                    <td>{{ $item['codigo'] ?? ($item['id'] ?? '-') }}</td>
                    <td>{{ $item['descripcion'] ?? 'Sin descripcion' }}</td>
                    <td class="text-right">${{ number_format($salePrice, 2) }}</td>
                    <td class="text-right">
                        @if ($supplierCost > 0)
                            ${{ number_format($supplierCost, 2) }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="text-right {{ $lineProfit < 0 ? 'text-danger' : 'text-success' }}">
                        ${{ number_format($lineProfit, 2) }}
                    </td>
                    <td class="text-right">${{ number_format($lineSalesTotal, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No hay productos</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @php
        $reportTotalDiscounts = (float) ($content['total_descuentos'] ?? 0);
        $reportGlobalDiscount = max($reportTotalDiscounts - $reportItemDiscounts, 0);
        $reportTotalSales = $reportTotalSalesBeforeDiscounts - $reportItemDiscounts - $reportGlobalDiscount;
        $reportTotalProfit = $reportTotalSales - $reportTotalCosts;
    @endphp

    <table class="summary">
        <tr>
            <td><b>Subtotal:</b></td>
            <td class="text-right">${{ number_format($reportTotalSalesBeforeDiscounts, 2) }}</td>
        </tr>
        <tr>
            <td><b>Descuento por items:</b></td>
            <td class="text-right">-${{ number_format($reportItemDiscounts, 2) }}</td>
        </tr>
        <tr>
            <td><b>Descuento global:</b></td>
            <td class="text-right">-${{ number_format($reportGlobalDiscount, 2) }}</td>
        </tr>
        <tr>
            <td><b>Total ventas netas:</b></td>
            <td class="text-right">${{ number_format($reportTotalSales, 2) }}</td>
        </tr>
        <tr>
            <td><b>Total de costos:</b></td>
            <td class="text-right">${{ number_format($reportTotalCosts, 2) }}</td>
        </tr>
        <tr>
            <td><b>Ganancia total:</b></td>
            <td class="text-right {{ $reportTotalProfit < 0 ? 'text-danger' : 'text-success' }}">
                ${{ number_format($reportTotalProfit, 2) }}
            </td>
        </tr>
    </table>
</body>

</html>
