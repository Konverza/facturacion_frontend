@extends('layouts.auth-template')
@section('title', 'Detalle cotizacion')

@section('content')
    <section class="my-4 px-4 pb-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl">
                {{ $quotation->name ?? 'Cotizacion' }}
            </h1>
            <div class="flex flex-wrap gap-2">
                <x-button type="a" href="{{ Route('business.quotations.index') }}" text="Volver" icon="arrow-left"
                    typeButton="secondary" class="w-full sm:w-auto" />
                <x-button type="a" href="{{ Route('business.quotations.pdf', $quotation->id) }}" text="Ver PDF"
                    icon="eye" typeButton="info" class="w-full sm:w-auto" target="_blank" rel="noopener noreferrer" />
                <x-button type="a" href="{{ Route('business.quotations.edit', $quotation->id) }}" text="Editar"
                    icon="pencil" typeButton="primary" class="w-full sm:w-auto" />
            </div>
        </div>

        <div class="mt-4 grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border border-gray-300 p-4 dark:border-gray-800">
                <h2 class="text-lg font-semibold text-primary-500 dark:text-primary-300">Cliente</h2>
                <ul class="mt-3 space-y-1 text-sm text-gray-700 dark:text-gray-300">
                    <li><b>Nombre:</b> {{ $content['customer']['nombre'] ?? 'N/A' }}</li>
                    <li><b>Documento:</b> {{ $content['customer']['numDocumento'] ?? 'N/A' }}</li>
                    <li><b>NRC:</b> {{ $content['customer']['nrc'] ?? 'N/A' }}</li>
                    <li><b>Correo:</b> {{ $content['customer']['correo'] ?? 'N/A' }}</li>
                    <li><b>Telefono:</b> {{ $content['customer']['telefono'] ?? 'N/A' }}</li>
                </ul>
            </div>

            <div class="rounded-lg border border-gray-300 p-4 dark:border-gray-800">
                <h2 class="text-lg font-semibold text-primary-500 dark:text-primary-300">Condiciones</h2>
                <ul class="mt-3 space-y-1 text-sm text-gray-700 dark:text-gray-300">
                    <li><b>Vigencia:</b> {{ $meta['vigencia_dias'] ?? 15 }} dias</li>
                    <li><b>Tiempo de entrega:</b> {{ $meta['tiempo_entrega'] ?? 'N/A' }}</li>
                    <li><b>Forma de pago:</b> {{ $meta['forma_pago_tipo'] ?? 'N/A' }}</li>
                    <li><b>Detalle pago:</b> {{ $meta['forma_pago_detalle'] ?? 'N/A' }}</li>
                </ul>
            </div>
        </div>

        <div class="mt-4 rounded-lg border border-gray-300 p-4 dark:border-gray-800">
            <h2 class="text-lg font-semibold text-primary-500 dark:text-primary-300">Detalle de productos</h2>
            <div class="mt-3">
                <x-table :datatable="false">
                    <x-slot name="thead">
                        <x-tr>
                            <x-th>#</x-th>
                            <x-th>Descripcion</x-th>
                            <x-th>Cantidad</x-th>
                            <x-th>Precio</x-th>
                            <x-th>Descuento</x-th>
                            <x-th :last="true">Total</x-th>
                        </x-tr>
                    </x-slot>
                    <x-slot name="tbody">
                        @forelse (($content['products'] ?? []) as $item)
                            @php
                                $qty = (float) ($item['cantidad'] ?? 0);
                                $price = (float) ($item['precio'] ?? ($item['precio_sin_tributos'] ?? 0));
                                $discount = (float) ($item['descuento'] ?? 0);
                                $line = $qty * $price - $discount;
                            @endphp
                            <x-tr :last="$loop->last">
                                <x-td>{{ $loop->iteration }}</x-td>
                                <x-td>{{ $item['descripcion'] ?? 'N/A' }}</x-td>
                                <x-td>{{ number_format($qty, 2) }}</x-td>
                                <x-td>${{ number_format($price, 2) }}</x-td>
                                <x-td>${{ number_format($discount, 2) }}</x-td>
                                <x-td :last="true">${{ number_format($line, 2) }}</x-td>
                            </x-tr>
                        @empty
                            <x-tr>
                                <x-td colspan="6" :last="true" class="py-6 text-center text-gray-500">No hay
                                    productos.</x-td>
                            </x-tr>
                        @endforelse
                    </x-slot>
                </x-table>
            </div>
        </div>

        <div class="mt-4 rounded-lg border border-gray-300 p-4 dark:border-gray-800">
            <h2 class="text-lg font-semibold text-primary-500 dark:text-primary-300">Resumen de cotizacion</h2>
            <div class="mt-3">
                <x-table :datatable="false">
                    <x-slot name="thead">
                        <x-tr>
                            <x-th>Concepto</x-th>
                            <x-th :last="true" class="text-right">Monto</x-th>
                        </x-tr>
                    </x-slot>
                    <x-slot name="tbody">
                        <x-tr>
                            <x-td>Subtotal</x-td>
                            <x-td :last="true"
                                class="text-right">${{ number_format((float) ($content['subtotal'] ?? 0), 2) }}</x-td>
                        </x-tr>

                        @if (($content['type'] ?? '01') !== '01' && (float) ($content['iva'] ?? 0) > 0)
                            <x-tr>
                                <x-td>Impuesto al valor agregado (13%)</x-td>
                                <x-td :last="true"
                                    class="text-right">${{ number_format((float) ($content['iva'] ?? 0), 2) }}</x-td>
                            </x-tr>
                        @endif

                        <x-tr>
                            <x-td>Descuento a operacion</x-td>
                            <x-td :last="true" class="text-right text-red-500">
                                @if ((float) ($content['total_descuentos'] ?? 0) > 0)
                                    - ${{ number_format((float) ($content['total_descuentos'] ?? 0), 2) }}
                                @else
                                    $0.00
                                @endif
                            </x-td>
                        </x-tr>

                        <x-tr :last="true">
                            <x-td class="font-semibold">Total a pagar</x-td>
                            <x-td :last="true" class="text-right font-semibold">
                                ${{ number_format((float) ($content['total_pagar'] ?? ($content['total'] ?? 0)), 2) }}
                            </x-td>
                        </x-tr>
                    </x-slot>
                </x-table>
            </div>
        </div>

        <div class="mt-4 rounded-lg border border-gray-300 p-4 dark:border-gray-800">
            <h2 class="text-lg font-semibold text-primary-500 dark:text-primary-300">Conversion a DTE</h2>
            @if ($quotation->linked_dte_code)
                <p class="mt-3 text-sm text-green-600 dark:text-green-400">
                    Esta cotizacion ya fue convertida y vinculada al DTE: <b>{{ $quotation->linked_dte_code }}</b>
                </p>
                <x-button type="a" href="{{ Route('business.documents.show', $quotation->linked_dte_code) }}" text="Ver DTE"
                    icon="eye" typeButton="primary" class="mt-3 max-w-[200px]" text="Ver detalles del DTE" />
            @else
                <form action="{{ Route('business.quotations.convert', $quotation->id) }}" method="POST" class="mt-3">
                    @csrf
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                        <div class="w-full sm:max-w-xs">
                            <x-select label="Documento a emitir" name="document_type" id="quotation_document_type"
                                :options="['01' => 'Factura', '03' => 'Credito fiscal']" selected="{{ $quotation->type ?? '01' }}"
                                value="{{ $quotation->type ?? '01' }}" :search="false" />
                        </div>
                        <x-button type="submit" text="Convertir a DTE" icon="arrow-right" typeButton="success"
                            class="w-full sm:w-auto" />
                    </div>
                </form>
            @endif
        </div>

        <div class="mt-4 rounded-lg border border-gray-300 p-4 dark:border-gray-800">
            <h2 class="text-lg font-semibold text-primary-500 dark:text-primary-300">Mensaje y terminos</h2>
            <p class="mt-3 text-sm text-gray-700 dark:text-gray-300">
                {{ $meta['thank_you_message'] ?? 'Gracias por su preferencia.' }}</p>
            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                {{ $meta['terms_conditions'] ?? 'Sin terminos adicionales.' }}</p>
        </div>
    </section>
@endsection
