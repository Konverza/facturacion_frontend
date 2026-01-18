@extends('layouts.auth-template')
@section('title', 'Historial de Traslados')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Historial de Traslados
            </h1>
            <div class="flex gap-3">
                <x-button 
                    type="button" 
                    text="Volver" 
                    icon="arrow-left" 
                    typeButton="secondary"
                    onclick="window.location.href='{{ route('business.inventory.pos.index') }}'"
                />
                <x-button 
                    type="button" 
                    text="Traslado Simple" 
                    icon="plus" 
                    typeButton="secondary"
                    onclick="window.location.href='{{ route('business.inventory.transfers.create') }}'"
                />
                <x-button 
                    type="button" 
                    text="Traslado Múltiple" 
                    icon="layers" 
                    typeButton="primary"
                    onclick="window.location.href='{{ route('business.inventory.transfers.create-multiple') }}'"
                />
            </div>
        </div>

        <!-- Dashboard de Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Total del Mes -->
            <div class="bg-white dark:bg-gray-950 rounded-lg shadow border border-gray-200 dark:border-gray-800 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <x-icon icon="calendar" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Transferencias (Mes)</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $estadisticas['total_mes'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Completadas -->
            <div class="bg-white dark:bg-gray-950 rounded-lg shadow border border-gray-200 dark:border-gray-800 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                        <x-icon icon="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Completadas</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $estadisticas['completadas'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Pendientes -->
            <div class="bg-white dark:bg-gray-950 rounded-lg shadow border border-gray-200 dark:border-gray-800 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                        <x-icon icon="clock" class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Pendientes</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $estadisticas['pendientes'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Devoluciones -->
            <div class="bg-white dark:bg-gray-950 rounded-lg shadow border border-gray-200 dark:border-gray-800 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-red-100 dark:bg-red-900 rounded-lg">
                        <x-icon icon="rotate-ccw" class="w-6 h-6 text-red-600 dark:text-red-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Devoluciones (Mes)</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $estadisticas['devoluciones_mes'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reportes y Productos Top -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Productos Más Transferidos -->
            <div class="bg-white dark:bg-gray-950 rounded-lg shadow border border-gray-200 dark:border-gray-800 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <x-icon icon="trending-up" class="inline w-5 h-5" />
                    Top 5 Productos Más Transferidos
                </h3>
                
                @if($estadisticas['productos_top']->isNotEmpty())
                    <div class="space-y-3">
                        @foreach($estadisticas['productos_top'] as $index => $producto)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900 rounded-lg">
                            <div class="flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400 font-bold text-sm">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $producto->descripcion }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $producto->codigo }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-primary-600 dark:text-primary-400">{{ $producto->total_transferencias }}x</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($producto->cantidad_total, 0) }} und</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-gray-500 dark:text-gray-400 py-4">No hay datos disponibles</p>
                @endif
            </div>

            <!-- Alertas y Pendientes -->
            <div class="bg-white dark:bg-gray-950 rounded-lg shadow border border-gray-200 dark:border-gray-800 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <x-icon icon="alert-circle" class="inline w-5 h-5" />
                    Alertas y Pendientes
                </h3>
                
                <div class="space-y-3">
                    @if($estadisticas['liquidaciones_pendientes'] > 0)
                    <div class="flex items-start gap-3 p-4 bg-orange-50 dark:bg-orange-950 border border-orange-200 dark:border-orange-800 rounded-lg">
                        <x-icon icon="alert-triangle" class="w-5 h-5 text-orange-600 dark:text-orange-400 flex-shrink-0 mt-0.5" />
                        <div>
                            <p class="text-sm font-medium text-orange-900 dark:text-orange-100">
                                {{ $estadisticas['liquidaciones_pendientes'] }} {{ $estadisticas['liquidaciones_pendientes'] === 1 ? 'Liquidación Pendiente' : 'Liquidaciones Pendientes' }}
                            </p>
                            <p class="text-xs text-orange-700 dark:text-orange-300 mt-1">
                                Devoluciones que requieren verificación de inventario
                            </p>
                        </div>
                    </div>
                    @endif

                    @if($estadisticas['discrepancias'] > 0)
                    <div class="flex items-start gap-3 p-4 bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 rounded-lg">
                        <x-icon icon="alert-circle" class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" />
                        <div>
                            <p class="text-sm font-medium text-red-900 dark:text-red-100">
                                {{ $estadisticas['discrepancias'] }} {{ $estadisticas['discrepancias'] === 1 ? 'Discrepancia Detectada' : 'Discrepancias Detectadas' }}
                            </p>
                            <p class="text-xs text-red-700 dark:text-red-300 mt-1">
                                Diferencias entre cantidades esperadas y reales en liquidaciones
                            </p>
                        </div>
                    </div>
                    @endif

                    @if($estadisticas['pendientes'] > 0)
                    <div class="flex items-start gap-3 p-4 bg-yellow-50 dark:bg-yellow-950 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                        <x-icon icon="clock" class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" />
                        <div>
                            <p class="text-sm font-medium text-yellow-900 dark:text-yellow-100">
                                {{ $estadisticas['pendientes'] }} {{ $estadisticas['pendientes'] === 1 ? 'Transferencia Pendiente' : 'Transferencias Pendientes' }}
                            </p>
                            <p class="text-xs text-yellow-700 dark:text-yellow-300 mt-1">
                                Transferencias iniciadas que no se han completado
                            </p>
                        </div>
                    </div>
                    @endif

                    @if($estadisticas['liquidaciones_pendientes'] === 0 && $estadisticas['discrepancias'] === 0 && $estadisticas['pendientes'] === 0)
                    <div class="flex items-center gap-3 p-4 bg-green-50 dark:bg-green-950 border border-green-200 dark:border-green-800 rounded-lg">
                        <x-icon icon="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400" />
                        <p class="text-sm font-medium text-green-900 dark:text-green-100">
                            ¡Todo en orden! No hay alertas pendientes
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white dark:bg-gray-950 rounded-lg shadow border border-gray-200 dark:border-gray-800 p-4 mb-6">
            <form method="GET" action="{{ route('business.inventory.transfers.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipo de Traslado</label>
                    <select name="tipo_traslado" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                        <option value="">Todos</option>
                        <option value="branch_to_pos" {{ request('tipo_traslado') == 'branch_to_pos' ? 'selected' : '' }}>Sucursal → POS</option>
                        <option value="pos_to_branch" {{ request('tipo_traslado') == 'pos_to_branch' ? 'selected' : '' }}>POS → Sucursal</option>
                        <option value="pos_to_pos" {{ request('tipo_traslado') == 'pos_to_pos' ? 'selected' : '' }}>POS → POS</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Estado</label>
                    <select name="estado" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                        <option value="">Todos</option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="completado" {{ request('estado') == 'completado' ? 'selected' : '' }}>Completado</option>
                        <option value="cancelado" {{ request('estado') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fecha Desde</label>
                    <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                </div>

                <div class="flex items-end">
                    <x-button type="submit" text="Filtrar" icon="filter" typeButton="primary" class="w-full" />
                </div>
            </form>
        </div>

        <!-- Tabla de traslados -->
        <div class="bg-white dark:bg-gray-950 rounded-lg shadow border border-gray-200 dark:border-gray-800">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-900 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">N° Transfer.</th>
                            <th scope="col" class="px-6 py-3">Fecha</th>
                            <th scope="col" class="px-6 py-3">Tipo</th>
                            <th scope="col" class="px-6 py-3">Productos</th>
                            <th scope="col" class="px-6 py-3">Origen</th>
                            <th scope="col" class="px-6 py-3">Destino</th>
                            <th scope="col" class="px-6 py-3">Cantidad</th>
                            <th scope="col" class="px-6 py-3">Usuario</th>
                            <th scope="col" class="px-6 py-3">Estado</th>
                            <th scope="col" class="px-6 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($traslados as $traslado)
                            <tr class="bg-white border-b dark:bg-gray-950 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-900">
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900 dark:text-white">
                                    {{ $traslado->numero_transferencia ?? '#' . $traslado->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $traslado->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        @if($traslado->tipo_traslado === 'branch_to_pos')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 w-fit">
                                                <x-icon icon="arrow-right" class="w-3 h-3 mr-1" />
                                                Sucursal → POS
                                            </span>
                                        @elseif($traslado->tipo_traslado === 'pos_to_branch')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400 w-fit">
                                                <x-icon icon="arrow-left" class="w-3 h-3 mr-1" />
                                                POS → Sucursal
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400 w-fit">
                                                <x-icon icon="arrows-right-left" class="w-3 h-3 mr-1" />
                                                POS → POS
                                            </span>
                                        @endif
                                        
                                        @if($traslado->es_devolucion)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 w-fit">
                                                <x-icon icon="rotate-ccw" class="w-3 h-3 mr-1" />
                                                Devolución
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($traslado->esTransferenciaMultiple())
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900/30 dark:text-primary-400">
                                                <x-icon icon="layers" class="w-3 h-3 mr-1" />
                                                {{ $traslado->items->count() }} items
                                            </span>
                                        </div>
                                    @else
                                        <div class="font-medium text-gray-900 dark:text-white">
                                            {{ $traslado->businessProduct->descripcion }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $traslado->businessProduct->codigo }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($traslado->sucursalOrigen)
                                        <div class="flex items-center">
                                            <x-icon icon="building" class="w-4 h-4 mr-1 text-gray-400" />
                                            <span class="text-sm">{{ $traslado->sucursalOrigen->nombre }}</span>
                                        </div>
                                    @elseif($traslado->puntoVentaOrigen)
                                        <div class="flex items-center">
                                            <x-icon icon="truck" class="w-4 h-4 mr-1 text-gray-400" />
                                            <span class="text-sm">{{ $traslado->puntoVentaOrigen->nombre }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($traslado->sucursalDestino)
                                        <div class="flex items-center">
                                            <x-icon icon="building" class="w-4 h-4 mr-1 text-gray-400" />
                                            <span class="text-sm">{{ $traslado->sucursalDestino->nombre }}</span>
                                        </div>
                                    @elseif($traslado->puntoVentaDestino)
                                        <div class="flex items-center">
                                            <x-icon icon="truck" class="w-4 h-4 mr-1 text-gray-400" />
                                            <span class="text-sm">{{ $traslado->puntoVentaDestino->nombre }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-medium">
                                    @if($traslado->esTransferenciaMultiple())
                                        {{ number_format($traslado->items->sum('cantidad_solicitada'), 2) }}
                                    @else
                                        {{ number_format($traslado->cantidad, 2) }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    {{ $traslado->user->name }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        @if($traslado->estado === 'completado')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 w-fit">
                                                <x-icon icon="check-circle" class="w-3 h-3 mr-1" />
                                                Completado
                                            </span>
                                        @elseif($traslado->estado === 'pendiente')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400 w-fit">
                                                <x-icon icon="clock" class="w-3 h-3 mr-1" />
                                                Pendiente
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 w-fit">
                                                <x-icon icon="x-circle" class="w-3 h-3 mr-1" />
                                                Cancelado
                                            </span>
                                        @endif
                                        
                                        @if($traslado->requiere_liquidacion && !$traslado->liquidacion_completada)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400 w-fit">
                                                <x-icon icon="alert-circle" class="w-3 h-3 mr-1" />
                                                Pend. Liquidación
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <a href="{{ route('business.inventory.transfers.show', $traslado->id) }}" class="font-medium text-primary-600 hover:underline dark:text-primary-500 text-sm">
                                            Ver detalles
                                        </a>
                                        @if($traslado->estado === 'completado')
                                            <a href="{{ route('business.inventory.transfers.pdf', $traslado->id) }}" target="_blank" class="font-medium text-blue-600 hover:underline dark:text-blue-500 text-sm">
                                                Descargar PDF
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <x-icon icon="inbox" class="mx-auto w-12 h-12 mb-2" />
                                    <p>No se encontraron traslados</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($traslados->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800">
                    {{ $traslados->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
