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
                    text="Nuevo Traslado" 
                    icon="plus" 
                    typeButton="primary"
                    onclick="window.location.href='{{ route('business.inventory.transfers.create') }}'"
                />
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
                            <th scope="col" class="px-6 py-3">Fecha</th>
                            <th scope="col" class="px-6 py-3">Tipo</th>
                            <th scope="col" class="px-6 py-3">Producto</th>
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
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $traslado->fecha_traslado->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($traslado->tipo_traslado === 'branch_to_pos')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                            <x-icon icon="arrow-right" class="w-3 h-3 mr-1" />
                                            Sucursal → POS
                                        </span>
                                    @elseif($traslado->tipo_traslado === 'pos_to_branch')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">
                                            <x-icon icon="arrow-left" class="w-3 h-3 mr-1" />
                                            POS → Sucursal
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400">
                                            <x-icon icon="arrows-right-left" class="w-3 h-3 mr-1" />
                                            POS → POS
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 dark:text-white">
                                        {{ $traslado->businessProduct->descripcion }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $traslado->businessProduct->codigo }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($traslado->sucursalOrigen)
                                        <div class="flex items-center">
                                            <x-icon icon="building" class="w-4 h-4 mr-1 text-gray-400" />
                                            {{ $traslado->sucursalOrigen->nombre }}
                                        </div>
                                    @elseif($traslado->puntoVentaOrigen)
                                        <div class="flex items-center">
                                            <x-icon icon="truck" class="w-4 h-4 mr-1 text-gray-400" />
                                            {{ $traslado->puntoVentaOrigen->nombre }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($traslado->sucursalDestino)
                                        <div class="flex items-center">
                                            <x-icon icon="building" class="w-4 h-4 mr-1 text-gray-400" />
                                            {{ $traslado->sucursalDestino->nombre }}
                                        </div>
                                    @elseif($traslado->puntoVentaDestino)
                                        <div class="flex items-center">
                                            <x-icon icon="truck" class="w-4 h-4 mr-1 text-gray-400" />
                                            {{ $traslado->puntoVentaDestino->nombre }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-medium">
                                    {{ number_format($traslado->cantidad, 2) }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $traslado->user->name }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($traslado->estado === 'completado')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                            <x-icon icon="check-circle" class="w-3 h-3 mr-1" />
                                            Completado
                                        </span>
                                    @elseif($traslado->estado === 'pendiente')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                            <x-icon icon="clock" class="w-3 h-3 mr-1" />
                                            Pendiente
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                            <x-icon icon="x-circle" class="w-3 h-3 mr-1" />
                                            Cancelado
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('business.inventory.transfers.show', $traslado->id) }}" class="font-medium text-primary-600 hover:underline dark:text-primary-500">
                                        Ver detalles
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
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
