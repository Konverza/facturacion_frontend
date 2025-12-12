@extends('layouts.auth-template')
@section('title', 'Stock de ' . $puntoVenta->nombre)
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                    Stock de {{ $puntoVenta->nombre }}
                </h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Inventario detallado del punto de venta
                </p>
            </div>
            <x-button 
                type="button" 
                text="Volver" 
                icon="arrow-left" 
                typeButton="secondary"
                onclick="window.location.href='{{ route('business.inventory.pos.index') }}'"
            />
        </div>

        <!-- Información del Punto de Venta -->
        <div class="bg-white dark:bg-gray-950 rounded-lg shadow border border-gray-200 dark:border-gray-800 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Punto de Venta</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $puntoVenta->nombre }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Sucursal</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $puntoVenta->sucursal->nombre }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Inventario</p>
                    @if($puntoVenta->has_independent_inventory)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            <x-icon icon="check-circle" class="w-4 h-4 mr-1" />
                            Independiente
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                            <x-icon icon="building-store" class="w-4 h-4 mr-1" />
                            Comparte con Sucursal
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Resumen de Stock -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-950 rounded-lg shadow border border-gray-200 dark:border-gray-800 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Productos</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stocks->count() }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                        <x-icon icon="box" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-950 rounded-lg shadow border border-gray-200 dark:border-gray-800 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Disponibles</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ $stocks->where('estado_stock', 'disponible')->count() }}
                        </p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                        <x-icon icon="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-950 rounded-lg shadow border border-gray-200 dark:border-gray-800 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Por Agotarse</p>
                        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                            {{ $stocks->where('estado_stock', 'por_agotarse')->count() }}
                        </p>
                    </div>
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-full">
                        <x-icon icon="alert-triangle" class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-950 rounded-lg shadow border border-gray-200 dark:border-gray-800 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Agotados</p>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                            {{ $stocks->where('estado_stock', 'agotado')->count() }}
                        </p>
                    </div>
                    <div class="p-3 bg-red-100 dark:bg-red-900 rounded-full">
                        <x-icon icon="x-circle" class="w-6 h-6 text-red-600 dark:text-red-400" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Stock -->
        <div class="bg-white dark:bg-gray-950 rounded-lg shadow border border-gray-200 dark:border-gray-800">
            <div class="p-6 border-b border-gray-200 dark:border-gray-800">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <x-icon icon="list" class="inline w-5 h-5" />
                        Productos en Stock
                    </h2>
                    <div class="flex gap-2">
                        <input 
                            type="text" 
                            id="search-product" 
                            placeholder="Buscar producto..." 
                            class="px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg text-sm bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                        />
                        <select 
                            id="filter-estado" 
                            class="px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg text-sm bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                        >
                            <option value="">Todos los estados</option>
                            <option value="disponible">Disponible</option>
                            <option value="por_agotarse">Por Agotarse</option>
                            <option value="agotado">Agotado</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-900 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Código</th>
                            <th scope="col" class="px-6 py-3">Producto</th>
                            <th scope="col" class="px-6 py-3 text-center">Stock Actual</th>
                            <th scope="col" class="px-6 py-3 text-center">Stock Mínimo</th>
                            <th scope="col" class="px-6 py-3 text-center">Estado</th>
                            <th scope="col" class="px-6 py-3 text-right">Precio</th>
                        </tr>
                    </thead>
                    <tbody id="stock-table-body">
                        @forelse($stocks as $stock)
                            <tr class="bg-white border-b dark:bg-gray-950 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-900 stock-row" 
                                data-codigo="{{ strtolower($stock->businessProduct->codigo) }}"
                                data-descripcion="{{ strtolower($stock->businessProduct->descripcion) }}"
                                data-estado="{{ $stock->estado_stock }}">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    {{ $stock->businessProduct->codigo }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $stock->businessProduct->descripcion }}
                                </td>
                                <td class="px-6 py-4 text-center font-semibold">
                                    {{ number_format($stock->stockActual, 2) }}
                                </td>
                                <td class="px-6 py-4 text-center text-gray-600 dark:text-gray-400">
                                    {{ number_format($stock->stockMinimo, 2) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($stock->estado_stock === 'disponible')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Disponible
                                        </span>
                                    @elseif($stock->estado_stock === 'por_agotarse')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                            Por Agotarse
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            Agotado
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right font-medium text-gray-900 dark:text-white">
                                    ${{ number_format($stock->businessProduct->precioUni, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <x-icon icon="inbox" class="inline w-8 h-8 mb-2" />
                                    <p>No hay productos en el inventario de este punto de venta</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        // Filtros de búsqueda y estado
        const searchInput = document.getElementById('search-product');
        const filterEstado = document.getElementById('filter-estado');
        const rows = document.querySelectorAll('.stock-row');

        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedEstado = filterEstado.value;

            rows.forEach(row => {
                const codigo = row.dataset.codigo;
                const descripcion = row.dataset.descripcion;
                const estado = row.dataset.estado;

                const matchesSearch = codigo.includes(searchTerm) || descripcion.includes(searchTerm);
                const matchesEstado = !selectedEstado || estado === selectedEstado;

                row.style.display = matchesSearch && matchesEstado ? '' : 'none';
            });
        }

        searchInput.addEventListener('input', filterTable);
        filterEstado.addEventListener('change', filterTable);
    </script>
    @endpush
@endsection
