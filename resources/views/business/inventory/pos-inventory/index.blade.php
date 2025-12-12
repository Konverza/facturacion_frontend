@extends('layouts.auth-template')
@section('title', 'Inventario por Punto de Venta')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Inventario por Punto de Venta
            </h1>
            <div class="flex gap-3">
                <x-button 
                    type="button" 
                    text="Ver Traslados" 
                    icon="truck" 
                    typeButton="secondary"
                    onclick="window.location.href='{{ route('business.inventory.transfers.index') }}'"
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

        <!-- Tabs de Sucursales -->
        <div class="mb-6">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" role="tablist">
                    @foreach($sucursales as $index => $sucursal)
                        <li class="me-2" role="presentation">
                            <button 
                                class="inline-block p-4 border-b-2 rounded-t-lg hover:text-primary-600 hover:border-primary-300 dark:hover:text-primary-400 
                                    {{ $index === 0 ? 'text-primary-600 border-primary-600 dark:text-primary-500 dark:border-primary-500' : 'border-transparent' }}"
                                type="button" 
                                role="tab"
                                data-sucursal-id="{{ $sucursal->id }}"
                                onclick="switchSucursal({{ $sucursal->id }}, '{{ $sucursal->nombre }}')">
                                <x-icon icon="building" class="inline w-4 h-4" />
                                {{ $sucursal->nombre }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Información de stock de la sucursal -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-950 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Stock en Sucursal</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1" id="stock-sucursal-total">
                            Cargando...
                        </p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-full">
                        <x-icon icon="building" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-950 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Puntos de Venta Activos</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1" id="pos-activos">
                            {{ $puntosVenta->count() }}
                        </p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-full">
                        <x-icon icon="truck" class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-950 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Stock en POS</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1" id="stock-pos-total">
                            Cargando...
                        </p>
                    </div>
                    <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-full">
                        <x-icon icon="package" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Listado de Puntos de Venta -->
        <div class="bg-white dark:bg-gray-950 rounded-lg shadow border border-gray-200 dark:border-gray-800">
            <div class="p-6 border-b border-gray-200 dark:border-gray-800">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Puntos de Venta
                </h3>
            </div>

            <div class="p-6" id="puntos-venta-container">
                @if($puntosVenta->isEmpty())
                    <div class="text-center py-8">
                        <x-icon icon="inbox" class="mx-auto w-12 h-12 text-gray-400" />
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            No hay puntos de venta con inventario independiente configurado.
                        </p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($puntosVenta as $pos)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900 dark:text-white">
                                            {{ $pos->nombre }}
                                        </h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $pos->codPuntoVenta }}
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                        <x-icon icon="check-circle" class="w-3 h-3 mr-1" />
                                        Activo
                                    </span>
                                </div>

                                <div class="space-y-2 mb-4">
                                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                        <x-icon icon="building" class="w-4 h-4 mr-2" />
                                        {{ $pos->sucursal->nombre }}
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                        <x-icon icon="package" class="w-4 h-4 mr-2" />
                                        <span class="stock-pos-count" data-pos-id="{{ $pos->id }}">
                                            Cargando...
                                        </span>
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    <button 
                                        type="button"
                                        onclick="viewPosStock({{ $pos->id }}, '{{ $pos->nombre }}')"
                                        class="flex-1 inline-flex justify-center items-center px-3 py-2 text-sm font-medium text-center text-white bg-primary-600 rounded-lg hover:bg-primary-700 focus:ring-4 focus:ring-primary-300 dark:bg-primary-500 dark:hover:bg-primary-600 dark:focus:ring-primary-800">
                                        <x-icon icon="eye" class="w-4 h-4 mr-1" />
                                        Ver Stock
                                    </button>
                                    <button 
                                        type="button"
                                        onclick="openAssignModal({{ $pos->id }}, '{{ $pos->nombre }}')"
                                        class="inline-flex justify-center items-center px-3 py-2 text-sm font-medium text-center text-primary-600 border border-primary-600 rounded-lg hover:bg-primary-50 dark:text-primary-400 dark:border-primary-400 dark:hover:bg-gray-900">
                                        <x-icon icon="arrow-right" class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        let currentSucursalId = {{ $sucursales->first()->id ?? 'null' }};

        function switchSucursal(sucursalId, nombreSucursal) {
            currentSucursalId = sucursalId;
            
            // Actualizar clases de las pestañas
            document.querySelectorAll('[role="tab"]').forEach(tab => {
                const tabSucursalId = parseInt(tab.getAttribute('data-sucursal-id'));
                if (tabSucursalId === sucursalId) {
                    tab.classList.add('text-primary-600', 'border-primary-600', 'dark:text-primary-500', 'dark:border-primary-500');
                    tab.classList.remove('border-transparent');
                } else {
                    tab.classList.remove('text-primary-600', 'border-primary-600', 'dark:text-primary-500', 'dark:border-primary-500');
                    tab.classList.add('border-transparent');
                }
            });
            
            loadSucursalData(sucursalId);
        }

        function loadSucursalData(sucursalId) {
            // Cargar stock de sucursal
            fetch(`{{ route('business.inventory.stock.get') }}?type=branch&id=${sucursalId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('stock-sucursal-total').textContent = 
                            parseFloat(data.total_stock || 0).toFixed(2) + ' unidades';
                    }
                })
                .catch(error => console.error('Error:', error));

            // Cargar stock de puntos de venta
            loadPosStocks();
        }

        function loadPosStocks() {
            const stockElements = document.querySelectorAll('.stock-pos-count');
            const promises = [];

            stockElements.forEach(element => {
                const posId = element.getAttribute('data-pos-id');
                
                const promise = fetch(`{{ route('business.inventory.stock.get') }}?type=pos&id=${posId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            element.textContent = `${data.total_productos || 0} productos (${parseFloat(data.total_stock || 0).toFixed(2)} unidades)`;
                            return parseFloat(data.total_stock || 0);
                        }
                        return 0;
                    })
                    .catch(error => {
                        element.textContent = 'Error al cargar';
                        console.error('Error:', error);
                        return 0;
                    });
                    
                promises.push(promise);
            });

            // Calcular total de stock en POS después de que todas las peticiones terminen
            Promise.all(promises).then(stocks => {
                const totalStockPos = stocks.reduce((sum, stock) => sum + stock, 0);
                document.getElementById('stock-pos-total').textContent = 
                    totalStockPos.toFixed(2) + ' unidades';
            });
        }

        function viewPosStock(posId, posNombre) {
            window.location.href = `{{ url('business/inventory/pos') }}/${posId}`;
        }

        function openAssignModal(posId, posNombre) {
            window.location.href = `{{ url('business/inventory/pos') }}/${posId}/assign`;
        }

        // Cargar datos iniciales
        document.addEventListener('DOMContentLoaded', function() {
            if (currentSucursalId) {
                loadSucursalData(currentSucursalId);
            }
        });
    </script>
    @endpush
@endsection
