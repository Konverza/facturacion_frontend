@extends('layouts.auth-template')
@section('title', 'Detalle de factura del bolsón')
@section('content')
    <section class="my-4 pb-4">
        <div class="mb-4 px-4">
            <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Factura #{{ $invoice->correlative }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Bolsón {{ $invoice->bag->bag_code }}</p>
        </div>

        <div class="px-4">
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-950">
                <h2 class="mb-2 text-lg font-semibold text-gray-700 dark:text-gray-200">Cliente</h2>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    <p><b>Nombre:</b> {{ data_get($invoice, 'customer_data.nombre_receptor', 'Consumidor final') }}</p>
                    <p><b>Documento:</b> {{ data_get($invoice, 'customer_data.numero_documento', 'N/D') }}</p>
                    <p><b>Correo:</b> {{ data_get($invoice, 'customer_data.correo', 'N/D') }}</p>
                    <p><b>Teléfono:</b> {{ data_get($invoice, 'customer_data.telefono', 'N/D') }}</p>
                </div>

                <h2 class="mt-4 mb-2 text-lg font-semibold text-gray-700 dark:text-gray-200">Productos</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600 dark:text-gray-300">Descripción</th>
                                <th class="px-3 py-2 text-right font-semibold text-gray-600 dark:text-gray-300">Cantidad</th>
                                <th class="px-3 py-2 text-right font-semibold text-gray-600 dark:text-gray-300">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($invoice->products as $product)
                                <tr>
                                    <td class="px-3 py-2 text-gray-700 dark:text-gray-200">{{ $product['descripcion'] ?? 'Producto' }}</td>
                                    <td class="px-3 py-2 text-right text-gray-700 dark:text-gray-200">{{ $product['cantidad'] ?? 0 }}</td>
                                    <td class="px-3 py-2 text-right text-gray-700 dark:text-gray-200">${{ number_format((float) ($product['total'] ?? 0), 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex justify-end text-sm text-gray-700 dark:text-gray-200">
                    <div>
                        <p><b>Total:</b> ${{ number_format((float) ($invoice->totals['total_pagar'] ?? 0), 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
