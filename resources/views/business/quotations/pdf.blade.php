<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Cotizacion {{ $quotation->id }}</title>
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
        .img { width: 52px; height: 52px; object-fit: cover; }
        .footer { margin-top: 18px; }
        .summary { margin-top: 10px; width: 45%; margin-left: auto; }
        .summary td { border: none; padding: 4px 0; }
    </style>
</head>

<body>
    @php
        $company = $companyData['data'] ?? $companyData;
        $products = $content['products'] ?? [];
        $validityDays = (int) ($meta['vigencia_dias'] ?? 15);
        $customer = $content['customer'] ?? [];
        $customerName = $customer['nombre'] ?? ($content['nombre_receptor'] ?? 'Cliente no especificado');
        $customerDocument = $customer['numDocumento'] ?? ($content['numero_documento'] ?? null);
        $customerNrc = $customer['nrc'] ?? ($content['nrc_customer'] ?? null);
        $customerPhone = $customer['telefono'] ?? ($content['telefono'] ?? null);
        $customerEmail = $customer['correo'] ?? ($content['correo'] ?? null);
    @endphp

    <table class="header">
        <tr>
            <td style="width: 22%;">
                @if ($logo)
                    <img src="{{ $logo }}" alt="Logo" class="logo" width="180" style="height:auto; width:auto; max-width:180px; max-height:120px;">
                @endif
            </td>
            <td style="width: 48%; text-align: center;">
                <div class="title">Cotizacion</div>
                {{-- <div>{{ $company['nombre'] ?? $business->name ?? 'N/A' }}</div>
                <div><b>NIT:</b> {{ $company['nit'] ?? $business->nit ?? 'N/A' }}</div>
                <div><b>NRC:</b> {{ $company['nrc'] ?? ($business->nrc ?? 'N/A') }}</div>
                <div><b>Correo:</b> {{ $company['correo'] ?? ($business->email ?? 'N/A') }}</div> --}}
            </td>
            <td style="width: 30%; text-align: right;">
                <div><b>Emitida:</b> {{ $quotation->updated_at?->format('d/m/Y') }}</div>
                <div><b>Vigencia:</b> {{ $validityDays }} dias</div>
                <div class="muted">Valida hasta {{ optional($quotation->updated_at)->addDays($validityDays)?->format('d/m/Y') }}</div>
            </td>
        </tr>
    </table>

    <table style="margin-top: 10px; border: none;">
        <tr>
            <td style="width: 60%; border: none; padding: 0; vertical-align: top;">
                <div><b>Cliente:</b> {{ $customerName }}</div>
                @if (!empty($customerDocument))
                    <div><b>Identificación:</b> {{ $customerDocument }}</div>
                @endif
                @if (!empty($customerNrc))
                    <div><b>NRC:</b> {{ $customerNrc }}</div>
                @endif
                @if (!empty($customerPhone))
                    <div><b>Telefono:</b> {{ $customerPhone }}</div>
                @endif
                @if (!empty($customerEmail))
                    <div><b>Correo:</b> {{ $customerEmail }}</div>
                @endif
            </td>
            <td style="width: 40%; border: none; padding: 0; vertical-align: top; text-align: left;">
                <div><b>Vendedor:</b> {{ $company['nombre'] ?? $business->name ?? 'N/A' }}</div>
                <div><b>NIT:</b> {{ $company['nit'] ?? $business->nit ?? 'N/A' }}</div>
                <div><b>NRC:</b> {{ $company['nrc'] ?? ($business->nrc ?? 'N/A') }}</div>
                <div><b>Correo:</b> {{ $company['correo'] ?? ($business->email ?? 'N/A') }}</div>
            </td>
        </tr>
    </table>

    <p style="margin-top: 10px;"><b>Presente</b></p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Cant.</th>
                <th>Codigo</th>
                <th>Descripcion</th>
                <th>Precio Unitario</th>
                <th>Descuento</th>
                <th>Total</th>
                <th>Imagen</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $idx => $item)
                @php
                    $qty = (float) ($item['cantidad'] ?? 0);
                    $unit = (float) ($item['precio'] ?? ($item['precio_sin_tributos'] ?? 0));
                    $discount = (float) ($item['descuento'] ?? 0);
                    $lineTotal = ($unit * $qty) - $discount;
                @endphp
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ number_format($qty, 2) }}</td>
                    <td>{{ $item['codigo'] ?? ($item['id'] ?? '-') }}</td>
                    <td>{{ $item['descripcion'] ?? 'Sin descripcion' }}</td>
                    <td>${{ number_format($unit, 2) }}</td>
                    <td>${{ number_format($discount, 2) }}</td>
                    <td>${{ number_format($lineTotal, 2) }}</td>
                    <td>
                        @if (!empty($item['image_url']))
                            <img src="{{ $item['image_url'] }}" class="img" alt="Producto">
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">No hay productos</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="summary">
        <tr>
            <td><b>Subtotal:</b></td>
            <td style="text-align:right;">${{ number_format((float) ($content['subtotal'] ?? 0), 2) }}</td>
        </tr>
        <tr>
            <td><b>IVA:</b></td>
            <td style="text-align:right;">${{ number_format((float) ($content['iva'] ?? 0), 2) }}</td>
        </tr>
        <tr>
            <td><b>Descuento:</b></td>
            <td style="text-align:right;">${{ number_format((float) ($content['total_descuentos'] ?? 0), 2) }}</td>
        </tr>
        <tr>
            <td><b>Total:</b></td>
            <td style="text-align:right;">${{ number_format((float) ($content['total_pagar'] ?? ($content['total'] ?? 0)), 2) }}</td>
        </tr>
    </table>

    <div class="footer">
        <p><b>{{ $meta['thank_you_message'] ?? 'Gracias por su preferencia.' }}</b></p>
        <p><b>Tiempo de entrega:</b> {{ $meta['tiempo_entrega'] ?? 'Coordinado con el cliente' }}</p>
        <p><b>Formas de pago:</b> {{ $meta['forma_pago_tipo'] ?? 'Contado' }}</p>
        <p><b>Terminos y condiciones:</b> {{ $meta['terms_conditions'] ?? 'Sin terminos adicionales.' }}</p>
    </div>
</body>

</html>
