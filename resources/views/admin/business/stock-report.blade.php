@extends('layouts.auth-template')
@section('title', 'Reporte de Movimientos - ' . $business->nombre)
@section('content')
    <section class="my-4 px-4">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-primary-500 dark:text-primary-300">
                    Reporte de Movimientos de Stock
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Negocio: <span class="font-semibold">{{ $business->nombre }}</span> ({{ $business->nit }})
                </p>
            </div>
            <div>
                <x-button type="a" href="{{ route('admin.business.index') }}" icon="arrow-left" typeButton="secondary"
                    text="Volver a negocios" />
            </div>
        </div>

        <!-- Estadísticas generales -->
        <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-950">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Productos</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $reporte->count() }}</p>
                    </div>
                    <x-icon icon="box" class="h-10 w-10 text-primary-500" />
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-950">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Con Diferencias</p>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                            {{ $reporte->where('tiene_diferencia', true)->count() }}
                        </p>
                    </div>
                    <x-icon icon="alert" class="h-10 w-10 text-red-500" />
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-950">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Entradas</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ number_format($reporte->sum('entradas'), 2) }}
                        </p>
                    </div>
                    <x-icon icon="arrow-up" class="h-10 w-10 text-green-500" />
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-950">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Salidas</p>
                        <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                            {{ number_format($reporte->sum('salidas'), 2) }}
                        </p>
                    </div>
                    <x-icon icon="arrow-down" class="h-10 w-10 text-orange-500" />
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="mb-4 flex gap-4">
            <div class="flex-1">
                <x-input type="text" placeholder="Buscar producto..." class="w-full" icon="search"
                    id="input-search-report" />
            </div>
            <div>
                <select id="filter-diferencias" 
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                    <option value="todos">Todos los productos</option>
                    <option value="con-diferencias">Solo con diferencias</option>
                    <option value="sin-diferencias">Sin diferencias</option>
                </select>
            </div>
        </div>

        <!-- Tabla de reporte -->
        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-950">
            <div class="overflow-x-auto">
                <x-table id="table-stock-report">
                    <x-slot name="thead">
                        <x-tr>
                            <x-th class="w-20">Código</x-th>
                            <x-th>Producto</x-th>
                            <x-th>Sucursal</x-th>
                            <x-th class="text-right">Stock Inicial</x-th>
                            <x-th class="text-right">Entradas</x-th>
                            <x-th class="text-right">Salidas</x-th>
                            <x-th class="text-right">Total Calculado</x-th>
                            <x-th class="text-right">Stock Actual (BD)</x-th>
                            <x-th class="text-right">Diferencia</x-th>
                            <x-th class="text-center">Movimientos</x-th>
                            <x-th :last="true" class="text-center">Estado</x-th>
                        </x-tr>
                    </x-slot>
                    <x-slot name="tbody">
                        @foreach ($reporte as $item)
                            <x-tr class="{{ $item['tiene_diferencia'] ? 'bg-red-50 dark:bg-red-900/10' : '' }}">
                                <x-td>{{ $item['codigo'] }}</x-td>
                                <x-td>{{ $item['descripcion'] }}</x-td>
                                <x-td>
                                    <span class="text-xs text-gray-600 dark:text-gray-400">
                                        {{ $item['sucursal'] }}
                                        <span class="text-gray-400">({{ $item['sucursal_codigo'] }})</span>
                                    </span>
                                </x-td>
                                <x-td class="text-right">{{ number_format($item['stock_inicial'], 2) }}</x-td>
                                <x-td class="text-right">
                                    <span class="font-medium text-green-600 dark:text-green-400">
                                        +{{ number_format($item['entradas'], 2) }}
                                    </span>
                                </x-td>
                                <x-td class="text-right">
                                    <span class="font-medium text-red-600 dark:text-red-400">
                                        -{{ number_format($item['salidas'], 2) }}
                                    </span>
                                </x-td>
                                <x-td class="text-right font-semibold">{{ number_format($item['total_calculado'], 2) }}</x-td>
                                <x-td class="text-right">{{ number_format($item['stock_actual_db'], 2) }}</x-td>
                                <x-td class="text-right">
                                    @if($item['tiene_diferencia'])
                                        <span class="font-bold {{ $item['diferencia'] > 0 ? 'text-orange-600 dark:text-orange-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ number_format($item['diferencia'], 2) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">0.00</span>
                                    @endif
                                </x-td>
                                <x-td class="text-center">
                                    <span class="rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900/20 dark:text-blue-300">
                                        {{ $item['total_movimientos'] }}
                                    </span>
                                </x-td>
                                <x-td :last="true" class="text-center">
                                    @if($item['tiene_diferencia'])
                                        <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-800 dark:bg-red-900/20 dark:text-red-300">
                                            <x-icon icon="alert" class="h-3 w-3" />
                                            Desajustado
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800 dark:bg-green-900/20 dark:text-green-300">
                                            <x-icon icon="check" class="h-3 w-3" />
                                            Correcto
                                        </span>
                                    @endif
                                </x-td>
                            </x-tr>
                        @endforeach
                    </x-slot>
                </x-table>
            </div>
        </div>

        <!-- Leyenda -->
        <div class="mt-6 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
            <div class="flex items-start">
                <x-icon icon="info" class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                <div class="ml-3 text-sm text-blue-700 dark:text-blue-300">
                    <p class="font-medium">Información del reporte</p>
                    <ul class="mt-2 list-inside list-disc space-y-1 text-xs">
                        <li><strong>Sucursal:</strong> Sucursal a la que pertenece el stock</li>
                        <li><strong>Stock Inicial:</strong> Valor registrado en stockInicial del producto</li>
                        <li><strong>Entradas:</strong> Suma de todos los movimientos de tipo "entrada"</li>
                        <li><strong>Salidas:</strong> Suma de todos los movimientos de tipo "salida"</li>
                        <li><strong>Total Calculado:</strong> Stock Inicial + Entradas - Salidas</li>
                        <li><strong>Stock Actual (BD):</strong> Valor actual en business_product_stock para esa sucursal</li>
                        <li><strong>Diferencia:</strong> Stock Actual (BD) - Total Calculado</li>
                        <li class="text-red-700 dark:text-red-300"><strong>Productos con diferencias necesitan reconstrucción de stock</strong></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Búsqueda en la tabla
                $('#input-search-report').on('keyup', function() {
                    const searchTerm = $(this).val().toLowerCase().trim();
                    
                    $('#table-stock-report tbody tr').each(function() {
                        const $row = $(this);
                        const codigo = $row.find('td:eq(0)').text().toLowerCase();
                        const producto = $row.find('td:eq(1)').text().toLowerCase();
                        const sucursal = $row.find('td:eq(2)').text().toLowerCase();
                        
                        if (codigo.includes(searchTerm) || producto.includes(searchTerm) || sucursal.includes(searchTerm)) {
                            $row.show();
                        } else {
                            $row.hide();
                        }
                    });
                });

                // Filtro por diferencias
                $('#filter-diferencias').on('change', function() {
                    const filter = $(this).val();
                    
                    $('#table-stock-report tbody tr').each(function() {
                        const $row = $(this);
                        const tieneDiferencia = $row.hasClass('bg-red-50') || $row.hasClass('dark:bg-red-900/10');
                        
                        if (filter === 'todos') {
                            $row.show();
                        } else if (filter === 'con-diferencias') {
                            if (tieneDiferencia) {
                                $row.show();
                            } else {
                                $row.hide();
                            }
                        } else if (filter === 'sin-diferencias') {
                            if (!tieneDiferencia) {
                                $row.show();
                            } else {
                                $row.hide();
                            }
                        }
                    });
                });

                // Limpiar búsqueda con Escape
                $('#input-search-report').on('keydown', function(e) {
                    if (e.key === 'Escape') {
                        $(this).val('');
                        $('#table-stock-report tbody tr').show();
                    }
                });
            });
        </script>
    @endpush
@endsection
