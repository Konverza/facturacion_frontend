@extends('layouts.auth-template')
@section('title', 'Detalle de Bolsón')
@section('content')
    <section class="my-4 pb-4">
        <div class="mb-4 flex items-center justify-between px-4">
            <div>
                <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Bolsón {{ $bag->bag_code }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $bag->bag_date->format('d/m/Y') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <x-button type="a" href="{{ route('business.invoice-bags.report-summary', $bag->id) }}"
                    typeButton="info" icon="report" text="PDF Resumen" size="small" target="_blank" rel="noopener" />
                <x-button type="a" href="{{ route('business.invoice-bags.report-detail', $bag->id) }}"
                    typeButton="default" icon="list" text="PDF Detalle" size="small" target="_blank" rel="noopener" />
                @if ($bag->status === 'open')
                    <form action="{{ route('business.invoice-bags.send', $bag->id) }}" method="POST">
                        @csrf
                        <x-button type="submit" typeButton="primary" icon="send" size="small" text="Enviar facturas a Hacienda" />
                    </form>
                @elseif ($bag->status === 'sent')
                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700 dark:bg-green-900/30 dark:text-green-300">
                        Enviado a Hacienda
                    </span>
                @else
                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700 dark:bg-gray-900/30 dark:text-gray-300">
                        Cerrado
                    </span>
                @endif
            </div>
        </div>

        <div class="px-4">
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-800">
                <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Correlativo</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Cliente</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Total</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Estado</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white dark:divide-gray-800 dark:bg-gray-950">
                        @forelse ($bagInvoices as $invoice)
                            <tr>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-200">#{{ $invoice->correlative }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-200">
                                    {{ data_get($invoice, 'customer_data.nombre_receptor', 'Consumidor final') }}
                                </td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-200">
                                    ${{ number_format((float) ($invoice->totals['total_pagar'] ?? 0), 2) }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-2 py-1 text-xs font-medium {{ $invoice->status === 'pending' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300' : ($invoice->status === 'voided' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300') }}">
                                        {{ $invoice->status === 'pending' ? 'Pendiente' : ($invoice->status === 'voided' ? 'Anulada' : 'Convertida a DTE') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <x-button type="a" href="{{ route('business.invoice-bags.invoice', $invoice->id) }}"
                                            typeButton="secondary" text="Ver" icon="eye" size="small" />
                                        <x-button type="a" href="{{ route('business.invoice-bags.ticket-pdf', $invoice->id) }}"
                                            typeButton="info" text="Imprimir ticket" icon="printer" size="small"
                                            target="_blank" rel="noopener" />
                                        @if ($invoice->status === 'pending' && $bag->status === 'open')
                                            <form action="{{ route('business.invoice-bags.convert', $invoice->id) }}" method="POST">
                                                @csrf
                                                <x-button type="submit" typeButton="primary" text="Convertir a DTE" icon="file-symlink" size="small" />
                                            </form>
                                            <form action="{{ route('business.invoice-bags.void', $invoice->id) }}" method="POST">
                                                @csrf
                                                <x-button type="submit" typeButton="danger" text="Anular" icon="x" size="small" />
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                    No hay facturas pendientes en este bolsón.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
