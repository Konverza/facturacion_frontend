@extends('layouts.auth-template')
@section('title', 'Bolsón de Facturas')
@section('content')
    <section class="my-4 pb-4">
        <div class="mb-4 flex items-center justify-between px-4">
            <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Bolsón de Facturas</h1>
        </div>

        <div class="px-4">
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-800">
                <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Código</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Fecha</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Estado</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Facturas</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Total</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white dark:divide-gray-800 dark:bg-gray-950">
                        @forelse ($bags as $bag)
                            <tr>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-200">{{ $bag->bag_code }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-200">{{ $bag->bag_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $isSent = $bag->status === 'sent';
                                        $isClosed = $bag->status === 'closed';
                                    @endphp
                                    <span class="rounded-full px-2 py-1 text-xs font-medium {{ $isSent ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : ($isClosed ? 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-300' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300') }}">
                                        {{ $isSent ? 'Enviado' : ($isClosed ? 'Cerrado' : 'Abierto') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-200">{{ $bag->bag_invoices_count }}</td>
                                <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-200">
                                    ${{ number_format((float) ($bag->bag_total ?? 0), 2) }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('business.invoice-bags.show', $bag->id) }}"
                                        class="inline-flex items-center gap-1 text-sm font-medium text-primary-500 hover:text-primary-600">
                                        Ver detalle
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                    No hay bolsones registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
