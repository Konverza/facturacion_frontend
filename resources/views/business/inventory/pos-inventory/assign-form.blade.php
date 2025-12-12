@extends('layouts.auth-template')
@section('title', 'Asignar Productos a ' . $puntoVenta->nombre)
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                    Asignar Productos a {{ $puntoVenta->nombre }}
                </h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Selecciona productos y cantidades para asignar al punto de venta
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Punto de Venta</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $puntoVenta->nombre }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Sucursal</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $puntoVenta->sucursal->nombre }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Productos Asignados</p>
                    <p class="text-lg font-semibold text-primary-600 dark:text-primary-400">{{ $stocksActuales->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Formulario de Asignación -->
        <div class="bg-white dark:bg-gray-950 rounded-lg shadow border border-gray-200 dark:border-gray-800 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">
                <x-icon icon="box" class="inline w-5 h-5" />
                Productos Disponibles en Sucursal
            </h2>

            <!-- Búsqueda -->
            <div class="mb-6">
                <input 
                    type="text" 
                    id="search-product" 
                    placeholder="Buscar por código o descripción..." 
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg text-sm bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                />
            </div>

            <!-- Tabla de Productos -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-900 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Código</th>
                            <th scope="col" class="px-6 py-3">Producto</th>
                            <th scope="col" class="px-6 py-3 text-center">Stock en Sucursal</th>
                            <th scope="col" class="px-6 py-3 text-center">Stock en POS</th>
                            <th scope="col" class="px-6 py-3 text-center">Cantidad a Trasladar</th>
                            <th scope="col" class="px-6 py-3 text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="products-table">
                        @forelse($productosDisponibles as $producto)
                            @php
                                $stockActualPOS = $stocksActuales->firstWhere('business_product_id', $producto->id);
                                $stockPOS = $stockActualPOS ? $stockActualPOS->stockActual : 0;
                            @endphp
                            <tr class="bg-white border-b dark:bg-gray-950 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-900 product-row"
                                data-codigo="{{ strtolower($producto->codigo) }}"
                                data-descripcion="{{ strtolower($producto->descripcion) }}"
                                data-product-id="{{ $producto->id }}">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    {{ $producto->codigo }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $producto->descripcion }}
                                </td>
                                <td class="px-6 py-4 text-center font-semibold">
                                    <span class="stock-sucursal">{{ number_format($producto->stock_sucursal, 2) }}</span>
                                </td>
                                <td class="px-6 py-4 text-center text-gray-600 dark:text-gray-400">
                                    <span class="stock-pos">{{ number_format($stockPOS, 2) }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <input 
                                        type="number" 
                                        min="0" 
                                        max="{{ $producto->stock_sucursal }}"
                                        step="0.01"
                                        value="0"
                                        class="cantidad-input w-24 px-3 py-1.5 border border-gray-300 dark:border-gray-700 rounded-lg text-center bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                                        data-max="{{ $producto->stock_sucursal }}"
                                        data-product-id="{{ $producto->id }}"
                                    />
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button 
                                        type="button"
                                        onclick="asignarProducto({{ $producto->id }})"
                                        class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg focus:ring-4 focus:ring-primary-300 dark:bg-primary-500 dark:hover:bg-primary-600 dark:focus:ring-primary-800 disabled:opacity-50 disabled:cursor-not-allowed"
                                        id="btn-asignar-{{ $producto->id }}"
                                    >
                                        <x-icon icon="arrow-right" class="w-4 h-4 mr-1" />
                                        Trasladar
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <x-icon icon="inbox" class="inline w-8 h-8 mb-2" />
                                    <p>No hay productos disponibles en la sucursal</p>
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
        // Búsqueda de productos
        const searchInput = document.getElementById('search-product');
        const rows = document.querySelectorAll('.product-row');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();

            rows.forEach(row => {
                const codigo = row.dataset.codigo;
                const descripcion = row.dataset.descripcion;
                const matches = codigo.includes(searchTerm) || descripcion.includes(searchTerm);
                row.style.display = matches ? '' : 'none';
            });
        });

        // Validación de cantidad
        document.querySelectorAll('.cantidad-input').forEach(input => {
            input.addEventListener('input', function() {
                const max = parseFloat(this.dataset.max);
                const value = parseFloat(this.value);

                if (value > max) {
                    this.value = max;
                }
                if (value < 0) {
                    this.value = 0;
                }
            });
        });

        // Asignar producto
        function asignarProducto(productId) {
            const input = document.querySelector(`input[data-product-id="${productId}"]`);
            const cantidad = parseFloat(input.value);

            if (!cantidad || cantidad <= 0) {
                alert('Por favor ingrese una cantidad válida');
                return;
            }

            const btn = document.getElementById(`btn-asignar-${productId}`);
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<svg class="inline w-4 h-4 animate-spin mr-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3a9 9 0 1 0 9 9" /></svg> Procesando...';

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('tipo_traslado', 'branch_to_pos');
            formData.append('business_product_id', productId);
            formData.append('cantidad', cantidad);
            formData.append('sucursal_origen_id', {{ $puntoVenta->sucursal_id }});
            formData.append('punto_venta_destino_id', {{ $puntoVenta->id }});
            formData.append('notas', 'Asignación desde formulario de inventario');

            fetch('{{ route('business.inventory.transfers.store') }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Producto trasladado exitosamente');
                    // Actualizar stocks en la tabla
                    const row = document.querySelector(`tr[data-product-id="${productId}"]`);
                    const stockSucursal = row.querySelector('.stock-sucursal');
                    const stockPOS = row.querySelector('.stock-pos');
                    
                    const currentStockSucursal = parseFloat(stockSucursal.textContent.replace(',', ''));
                    const currentStockPOS = parseFloat(stockPOS.textContent.replace(',', ''));
                    
                    stockSucursal.textContent = (currentStockSucursal - cantidad).toFixed(2);
                    stockPOS.textContent = (currentStockPOS + cantidad).toFixed(2);
                    
                    // Actualizar max del input
                    input.setAttribute('max', currentStockSucursal - cantidad);
                    input.dataset.max = currentStockSucursal - cantidad;
                    input.value = 0;
                    
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                } else {
                    alert('Error: ' + data.message);
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar el traslado');
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        }
    </script>
    @endpush
@endsection
