@extends('layouts.auth-template')
@section('title', 'Detalle de Traslado')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                    Detalle de Traslado #{{ $traslado->id }}
                </h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Información completa del traslado realizado
                </p>
            </div>
            <x-button 
                type="button" 
                text="Volver" 
                icon="arrow-left" 
                typeButton="secondary"
                onclick="window.location.href='{{ route('business.inventory.transfers.index') }}'"
            />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Estado del Traslado -->
            <div class="lg:col-span-3">
                <div class="bg-white dark:bg-gray-950 rounded-lg shadow border border-gray-200 dark:border-gray-800 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                Estado del Traslado
                            </h3>
                            <div class="flex items-center gap-4">
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
                                
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    Creado: {{ $traslado->created_at->format('d/m/Y H:i') }}
                                </span>
                            </div>
                        </div>
                        
                        @if($traslado->estado === 'pendiente')
                            <x-button 
                                type="button" 
                                text="Cancelar Traslado" 
                                icon="x" 
                                typeButton="danger"
                                onclick="cancelarTraslado({{ $traslado->id }})"
                            />
                        @endif
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

            <!-- Información del Producto -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-950 rounded-lg shadow border border-gray-200 dark:border-gray-800 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <x-icon icon="box" class="inline w-5 h-5" />
                        Producto
                    </h3>
                    
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
                        
                        <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
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
            </div>
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
