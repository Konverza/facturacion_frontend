@extends('layouts.auth-template')
@section('title', 'Detalle de Traslado')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                    {{ $traslado->es_devolucion ? '(Devolución)' : '' }} Detalle de Traslado
                </h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    {{ $traslado->numero_transferencia ?? '#' . $traslado->id }}
                    @if($traslado->es_devolucion)
                        <span class="ml-2 text-red-600 dark:text-red-400 font-semibold">• Devolución Masiva</span>
                    @endif
                </p>
            </div>
            <div class="flex gap-2">
                <x-button 
                    type="button" 
                    text="Volver" 
                    icon="arrow-left" 
                    typeButton="secondary"
                    onclick="window.location.href='{{ route('business.inventory.transfers.index') }}'"
                />
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Estado y Acciones -->
            <div class="lg:col-span-3">
                <div class="bg-white dark:bg-gray-950 rounded-lg shadow border border-gray-200 dark:border-gray-800 p-6">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                Estado del Traslado
                            </h3>
                            <div class="flex items-center gap-4 flex-wrap">
                                @if($traslado->estado === 'completado')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        <x-icon icon="check-circle" class="w-4 h-4 mr-1" />
                                        Completado
                                    </span>
                                @elseif($traslado->estado === 'pendiente')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        <x-icon icon="clock" class="w-4 h-4 mr-1" />
                                        Pendiente
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        <x-icon icon="x-circle" class="w-4 h-4 mr-1" />
                                        Cancelado
                                    </span>
                                @endif

                                @if($traslado->esTransferenciaMultiple())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        <x-icon icon="layers" class="w-4 h-4 mr-1" />
                                        Múltiple ({{ $traslado->items->count() }} items)
                                    </span>
                                @endif

                                @if($traslado->requiere_liquidacion)
                                    @if($traslado->liquidacion_completada)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            <x-icon icon="check-circle" class="w-4 h-4 mr-1" />
                                            Liquidado
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                            <x-icon icon="alert-circle" class="w-4 h-4 mr-1" />
                                            Pendiente Liquidación
                                        </span>
                                    @endif
                                @endif
                                
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $traslado->created_at->format('d/m/Y H:i') }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="flex gap-2 flex-wrap">
                            @if($traslado->estado === 'completado')
                                <!-- Botones de descarga -->
                                <x-button 
                                    type="button" 
                                    text="PDF" 
                                    icon="download" 
                                    typeButton="primary"
                                    size="small"
                                    onclick="window.open('{{ route('business.inventory.transfers.pdf', $traslado->id) }}', '_blank')"
                                />
                                
                                @if($traslado->es_devolucion)
                                    <x-button 
                                        type="button" 
                                        text="PDF Devolución" 
                                        icon="download" 
                                        typeButton="secondary"
                                        size="small"
                                        onclick="window.open('{{ route('business.inventory.transfers.pdf-devolucion', $traslado->id) }}', '_blank')"
                                    />
                                @endif

                                @if($traslado->requiere_liquidacion && !$traslado->liquidacion_completada)
                                    <x-button 
                                        type="button" 
                                        text="Completar Liquidación" 
                                        icon="clipboard-check" 
                                        typeButton="warning"
                                        size="small"
                                        onclick="window.location.href='{{ route('business.inventory.transfers.liquidacion-form', $traslado->id) }}'"
                                    />
                                @endif

                                @if($traslado->liquidacion_completada)
                                    <x-button 
                                        type="button" 
                                        text="PDF Liquidación" 
                                        icon="download" 
                                        typeButton="success"
                                        size="small"
                                        onclick="window.open('{{ route('business.inventory.transfers.pdf-liquidacion', $traslado->id) }}', '_blank')"
                                    />
                                @endif
                            @endif

                            @if($traslado->estado === 'pendiente')
                                @if($traslado->requiere_liquidacion && !$traslado->liquidacion_completada)
                                    <x-button 
                                        type="button" 
                                        text="Ir a Liquidación" 
                                        icon="clipboard-check" 
                                        typeButton="warning"
                                        size="small"
                                        onclick="window.location.href='{{ route('business.inventory.transfers.liquidacion-form', $traslado->id) }}'"
                                    />
                                @endif
                                
                                <x-button 
                                    type="button" 
                                    text="Cancelar" 
                                    icon="x" 
                                    typeButton="danger"
                                    size="small"
                                    onclick="cancelarTraslado({{ $traslado->id }})"
                                />
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del Traslado -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-950 rounded-lg shadow border border-gray-200 dark:border-gray-800 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <x-icon icon="info-circle" class="inline w-5 h-5" />
                        Información del Traslado
                    </h3>
                    
                    <div class="space-y-4">
                        <!-- Tipo de Traslado -->
                        <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-900 rounded-lg">
                            <x-icon icon="arrows-right-left" class="w-5 h-5 text-primary-500" />
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Tipo de Traslado</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    @if($traslado->tipo_traslado === 'branch_to_pos')
                                        Sucursal → Punto de Venta
                                    @elseif($traslado->tipo_traslado === 'pos_to_branch')
                                        Punto de Venta → Sucursal
                                    @else
                                        Punto de Venta → Punto de Venta
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Origen -->
                        <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-900 rounded-lg">
                            <x-icon icon="map-pin" class="w-5 h-5 text-blue-500 mt-1" />
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Origen</p>
                                @if($traslado->sucursalOrigen)
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        Sucursal: {{ $traslado->sucursalOrigen->nombre }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $traslado->sucursalOrigen->direccion }}
                                    </p>
                                @else
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        Punto de Venta: {{ $traslado->puntoVentaOrigen->nombre }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $traslado->puntoVentaOrigen->direccion }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Destino -->
                        <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-900 rounded-lg">
                            <x-icon icon="flag" class="w-5 h-5 text-green-500 mt-1" />
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Destino</p>
                                @if($traslado->sucursalDestino)
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        Sucursal: {{ $traslado->sucursalDestino->nombre }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $traslado->sucursalDestino->direccion }}
                                    </p>
                                @else
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        Punto de Venta: {{ $traslado->puntoVentaDestino->nombre }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $traslado->puntoVentaDestino->direccion }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Notas -->
                        @if($traslado->notas)
                            <div class="p-3 bg-gray-50 dark:bg-gray-900 rounded-lg">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Notas</p>
                                <p class="text-sm text-gray-900 dark:text-white">{{ $traslado->notas }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Productos Transferidos -->
            <div class="lg:col-span-3">
                <div class="bg-white dark:bg-gray-950 rounded-lg shadow border border-gray-200 dark:border-gray-800 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <x-icon icon="box" class="inline w-5 h-5" />
                        {{ $traslado->esTransferenciaMultiple() ? 'Productos Transferidos' : 'Producto Transferido' }}
                    </h3>
                    
                    @if($traslado->esTransferenciaMultiple())
                        <!-- Lista de múltiples productos -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-900 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-4 py-3">#</th>
                                        <th scope="col" class="px-4 py-3">Código</th>
                                        <th scope="col" class="px-4 py-3">Descripción</th>
                                        <th scope="col" class="px-4 py-3 text-center">Cantidad</th>
                                        @if($traslado->requiere_liquidacion)
                                            <th scope="col" class="px-4 py-3 text-center">Cant. Real</th>
                                            <th scope="col" class="px-4 py-3 text-center">Diferencia</th>
                                        @endif
                                        <th scope="col" class="px-4 py-3">Nota</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($traslado->items as $item)
                                    <tr class="border-b dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-900">
                                        <td class="px-4 py-3">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-3 font-medium">{{ $item->businessProduct->codigo }}</td>
                                        <td class="px-4 py-3">{{ $item->businessProduct->descripcion }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="font-semibold text-blue-600 dark:text-blue-400">
                                                {{ number_format($item->cantidad_solicitada, 2) }}
                                            </span>
                                        </td>
                                        @if($traslado->requiere_liquidacion)
                                            <td class="px-4 py-3 text-center">
                                                @if($traslado->liquidacion_completada)
                                                    <span class="font-semibold">{{ number_format($item->cantidad_real, 2) }}</span>
                                                @else
                                                    <span class="text-gray-400">Pendiente</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                @if($traslado->liquidacion_completada)
                                                    @if($item->diferencia > 0)
                                                        <span class="text-green-600 dark:text-green-400 font-semibold">
                                                            +{{ number_format($item->diferencia, 2) }}
                                                        </span>
                                                    @elseif($item->diferencia < 0)
                                                        <span class="text-red-600 dark:text-red-400 font-semibold">
                                                            {{ number_format($item->diferencia, 2) }}
                                                        </span>
                                                    @else
                                                        <span class="text-gray-600 dark:text-gray-400">0.00</span>
                                                    @endif
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                        @endif
                                        <td class="px-4 py-3">
                                            @if($item->nota_item)
                                                <span class="text-xs italic text-gray-600 dark:text-gray-400">
                                                    {{ $item->nota_item }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="text-xs font-semibold text-gray-700 uppercase bg-gray-50 dark:bg-gray-900 dark:text-gray-400">
                                    <tr>
                                        <td colspan="3" class="px-4 py-3 text-right">Total:</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-primary-600 dark:text-primary-400 font-bold">
                                                {{ number_format($traslado->items->sum('cantidad_solicitada'), 2) }}
                                            </span>
                                        </td>
                                        @if($traslado->requiere_liquidacion)
                                            <td class="px-4 py-3 text-center">
                                                @if($traslado->liquidacion_completada)
                                                    <span class="font-bold">
                                                        {{ number_format($traslado->items->sum('cantidad_real'), 2) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                @if($traslado->liquidacion_completada)
                                                    @php
                                                        $diferenciaTotal = $traslado->items->sum('diferencia');
                                                    @endphp
                                                    @if($diferenciaTotal != 0)
                                                        <span class="{{ $diferenciaTotal > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} font-bold">
                                                            {{ $diferenciaTotal > 0 ? '+' : '' }}{{ number_format($diferenciaTotal, 2) }}
                                                        </span>
                                                    @else
                                                        <span class="text-gray-600 dark:text-gray-400 font-bold">0.00</span>
                                                    @endif
                                                @endif
                                            </td>
                                        @endif
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <!-- Producto único (legacy) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Código</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $traslado->businessProduct->codigo }}
                                    </p>
                                </div>
                                
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Descripción</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $traslado->businessProduct->descripcion }}
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Cantidad Trasladada</p>
                                    <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                                        {{ number_format($traslado->cantidad, 2) }}
                                    </p>
                                </div>
                                
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Precio Unitario</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        ${{ number_format($traslado->businessProduct->precioUni, 2) }}
                                    </p>
                                </div>
                                
                                <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Valor Total</p>
                                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                                        ${{ number_format($traslado->cantidad * $traslado->businessProduct->precioUni, 2) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @if($traslado->liquidacion_completada && $traslado->observaciones_liquidacion)
            <!-- Observaciones de Liquidación -->
            <div class="lg:col-span-3">
                <div class="bg-yellow-50 dark:bg-yellow-950 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100 mb-3">
                        <x-icon icon="alert-triangle" class="inline w-5 h-5" />
                        Observaciones de Liquidación
                    </h3>
                    <p class="text-sm text-yellow-800 dark:text-yellow-200">
                        {{ $traslado->observaciones_liquidacion }}
                    </p>
                </div>
            </div>
            @endif
        </div>
    </section>

    @push('scripts')
    <script>
        function cancelarTraslado(id) {
            if (!confirm('¿Está seguro de cancelar este traslado? Esta acción no se puede deshacer.')) {
                return;
            }

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');

            fetch(`{{ url('business/inventory/transfers') }}/${id}/cancel`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Traslado cancelado exitosamente');
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cancelar el traslado');
            });
        }
    </script>
    @endpush
@endsection
