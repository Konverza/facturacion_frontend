@extends('layouts.auth-template')
@section('title', 'Crear Traslado Múltiple')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                    Crear Traslado Múltiple
                </h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Trasladar varios productos a la vez entre sucursales y puntos de venta
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

        <div class="bg-white dark:bg-gray-950 rounded-lg shadow border border-gray-200 dark:border-gray-800">
            <form id="form-traslado-multiple" class="p-6 space-y-6">
                @csrf

                <!-- Tipo de Traslado -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        <x-icon icon="arrows-right-left" class="inline w-4 h-4" />
                        Tipo de Traslado
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="tipo-card relative flex cursor-pointer rounded-lg border-2 border-primary-500 dark:border-primary-600 bg-white dark:bg-gray-900 p-4 shadow-sm">
                            <input type="radio" name="tipo_traslado" value="branch_to_pos" class="sr-only" checked>
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="block text-sm font-medium text-gray-900 dark:text-white">
                                        <x-icon icon="arrow-right" class="inline w-4 h-4" />
                                        Sucursal → Punto de Venta
                                    </span>
                                    <span class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Asignar productos a un punto de venta
                                    </span>
                                </span>
                            </span>
                            <x-icon icon="check-circle" class="h-5 w-5 text-primary-600 tipo-check" />
                        </label>

                        <label class="tipo-card relative flex cursor-pointer rounded-lg border-2 border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
                            <input type="radio" name="tipo_traslado" value="pos_to_branch" class="sr-only">
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="block text-sm font-medium text-gray-900 dark:text-white">
                                        <x-icon icon="arrow-left" class="inline w-4 h-4" />
                                        Punto de Venta → Sucursal
                                    </span>
                                    <span class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Devolver productos a la sucursal
                                    </span>
                                </span>
                            </span>
                            <x-icon icon="check-circle" class="h-5 w-5 text-primary-600 tipo-check hidden" />
                        </label>

                        <label class="tipo-card relative flex cursor-pointer rounded-lg border-2 border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
                            <input type="radio" name="tipo_traslado" value="pos_to_pos" class="sr-only">
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="block text-sm font-medium text-gray-900 dark:text-white">
                                        <x-icon icon="arrows-right-left" class="inline w-4 h-4" />
                                        Punto de Venta → Punto de Venta
                                    </span>
                                    <span class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Trasladar entre puntos de venta
                                    </span>
                                </span>
                            </span>
                            <x-icon icon="check-circle" class="h-5 w-5 text-primary-600 tipo-check hidden" />
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Origen -->
                    <div id="origen-container">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            <x-icon icon="map-pin" class="inline w-4 h-4" />
                            Origen
                        </label>
                        <select id="select-origen" name="origen_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-900 dark:border-gray-700 dark:text-white" required>
                            <option value="">Seleccionar origen...</option>
                            @foreach($sucursales as $sucursal)
                                <option value="{{ $sucursal->id }}" data-tipo="branch">{{ $sucursal->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Destino -->
                    <div id="destino-container">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            <x-icon icon="flag" class="inline w-4 h-4" />
                            Destino
                        </label>
                        <select id="select-destino" name="destino_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-900 dark:border-gray-700 dark:text-white" required>
                            <option value="">Seleccionar destino...</option>
                            @foreach($puntosVenta as $pos)
                                <option value="{{ $pos->id }}" data-tipo="pos">{{ $pos->nombre }} ({{ $pos->sucursal->nombre }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Productos a Trasladar -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            <x-icon icon="box" class="inline w-5 h-5" />
                            Productos a Trasladar
                        </h3>
                        <x-button 
                            type="button" 
                            text="Agregar Producto" 
                            icon="plus" 
                            typeButton="primary"
                            size="small"
                            id="btn-agregar-producto"
                        />
                    </div>

                    <div id="productos-container" class="space-y-4">
                        <!-- Los productos se agregan dinámicamente -->
                    </div>

                    <div id="empty-state" class="text-center py-12 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg">
                        <x-icon icon="box" class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600" />
                        <p class="mt-2 text-gray-500 dark:text-gray-400">
                            No hay productos agregados. Haz clic en "Agregar Producto" para comenzar.
                        </p>
                    </div>
                </div>

                <!-- Notas -->
                <div>
                    <label for="notas" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        <x-icon icon="note" class="inline w-4 h-4" />
                        Notas (opcional)
                    </label>
                    <textarea id="notas" name="notas" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-900 dark:border-gray-700 dark:text-white" placeholder="Observaciones sobre el traslado..."></textarea>
                </div>

                <!-- Resumen -->
                <div id="resumen-traslado" class="hidden bg-primary-50 dark:bg-primary-950 border border-primary-200 dark:border-primary-800 rounded-lg p-4">
                    <h4 class="font-semibold text-primary-900 dark:text-primary-100 mb-2">Resumen del Traslado</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <p class="text-primary-600 dark:text-primary-400">Total Items:</p>
                            <p id="total-items" class="text-lg font-bold text-primary-900 dark:text-primary-100">0</p>
                        </div>
                        <div>
                            <p class="text-primary-600 dark:text-primary-400">Cantidad Total:</p>
                            <p id="cantidad-total" class="text-lg font-bold text-primary-900 dark:text-primary-100">0</p>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <x-button 
                        type="button" 
                        text="Cancelar" 
                        typeButton="secondary"
                        onclick="window.location.href='{{ route('business.inventory.transfers.index') }}'"
                    />
                    <x-button 
                        type="submit" 
                        text="Crear Traslado" 
                        icon="check" 
                        typeButton="primary"
                        id="btn-submit"
                    />
                </div>
            </form>
        </div>
    </section>

    <!-- Modal Agregar Producto -->
    <div id="modal-agregar-producto" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75" onclick="cerrarModalProducto()"></div>

            <div class="inline-block align-middle bg-white dark:bg-gray-950 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full border border-gray-200 dark:border-gray-800">
                <div class="bg-white dark:bg-gray-950 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        Agregar Producto al Traslado
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Producto</label>
                            <select id="modal-producto" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                                <option value="">Seleccionar producto...</option>
                            </select>
                        </div>

                        <div id="modal-stock-info" class="hidden bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-800 rounded-lg p-3 text-sm">
                            <p class="text-blue-900 dark:text-blue-100">
                                <strong>Stock disponible:</strong> <span id="modal-stock-disponible">0</span>
                            </p>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cantidad</label>
                            <input type="number" id="modal-cantidad" min="0.01" step="0.01" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-900 dark:border-gray-700 dark:text-white" placeholder="0.00">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nota (opcional)</label>
                            <textarea id="modal-nota" rows="2" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-900 dark:border-gray-700 dark:text-white" placeholder="Observaciones..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                    <x-button 
                        type="button" 
                        text="Agregar" 
                        icon="plus" 
                        typeButton="primary"
                        onclick="confirmarAgregarProducto()"
                    />
                    <x-button 
                        type="button" 
                        text="Cancelar" 
                        typeButton="secondary"
                        onclick="cerrarModalProducto()"
                    />
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let productosSeleccionados = [];
        let productosDisponibles = [];
        let origenActual = null;
        let tipoOrigenActual = 'branch';

        // Cambio de tipo de traslado
        document.querySelectorAll('input[name="tipo_traslado"]').forEach(radio => {
            radio.addEventListener('change', function() {
                actualizarUITipoTraslado();
                cargarOrigenesDestinos();
                limpiarProductos();
            });
        });

        function actualizarUITipoTraslado() {
            const tipoSeleccionado = document.querySelector('input[name="tipo_traslado"]:checked').value;
            
            document.querySelectorAll('.tipo-card').forEach(card => {
                const radio = card.querySelector('input[type="radio"]');
                const check = card.querySelector('.tipo-check');
                
                if (radio.checked) {
                    card.classList.add('border-primary-500', 'dark:border-primary-600');
                    card.classList.remove('border-gray-300', 'dark:border-gray-700');
                    check.classList.remove('hidden');
                } else {
                    card.classList.remove('border-primary-500', 'dark:border-primary-600');
                    card.classList.add('border-gray-300', 'dark:border-gray-700');
                    check.classList.add('hidden');
                }
            });
        }

        function cargarOrigenesDestinos() {
            const tipo = document.querySelector('input[name="tipo_traslado"]:checked').value;
            const selectOrigen = document.getElementById('select-origen');
            const selectDestino = document.getElementById('select-destino');

            selectOrigen.innerHTML = '<option value="">Seleccionar origen...</option>';
            selectDestino.innerHTML = '<option value="">Seleccionar destino...</option>';

            if (tipo === 'branch_to_pos') {
                tipoOrigenActual = 'branch';
                @foreach($sucursales as $sucursal)
                    selectOrigen.innerHTML += '<option value="{{ $sucursal->id }}" data-tipo="branch">{{ $sucursal->nombre }}</option>';
                @endforeach
                @foreach($puntosVenta as $pos)
                    selectDestino.innerHTML += '<option value="{{ $pos->id }}" data-tipo="pos">{{ $pos->nombre }} ({{ $pos->sucursal->nombre }})</option>';
                @endforeach
            } else if (tipo === 'pos_to_branch') {
                tipoOrigenActual = 'pos';
                @foreach($puntosVenta as $pos)
                    selectOrigen.innerHTML += '<option value="{{ $pos->id }}" data-tipo="pos">{{ $pos->nombre }} ({{ $pos->sucursal->nombre }})</option>';
                @endforeach
                @foreach($sucursales as $sucursal)
                    selectDestino.innerHTML += '<option value="{{ $sucursal->id }}" data-tipo="branch">{{ $sucursal->nombre }}</option>';
                @endforeach
            } else if (tipo === 'pos_to_pos') {
                tipoOrigenActual = 'pos';
                @foreach($puntosVenta as $pos)
                    selectOrigen.innerHTML += '<option value="{{ $pos->id }}" data-tipo="pos">{{ $pos->nombre }} ({{ $pos->sucursal->nombre }})</option>';
                    selectDestino.innerHTML += '<option value="{{ $pos->id }}" data-tipo="pos">{{ $pos->nombre }} ({{ $pos->sucursal->nombre }})</option>';
                @endforeach
            }
        }

        // Cargar productos disponibles cuando se selecciona origen
        document.getElementById('select-origen').addEventListener('change', function() {
            origenActual = this.value;
            if (origenActual) {
                cargarProductosDisponibles();
            }
            limpiarProductos();
        });

        async function cargarProductosDisponibles() {
            if (!origenActual) return;

            try {
                const tipo = document.querySelector('input[name="tipo_traslado"]:checked').value;
                const response = await axios.post('{{ route("business.inventory.transfers.products.available") }}', {
                    tipo_traslado: tipo,
                    origen_id: origenActual
                });

                if (response.data.success) {
                    productosDisponibles = response.data.productos || [];
                    console.log('Productos disponibles cargados:', productosDisponibles.length);
                } else {
                    productosDisponibles = [];
                    console.warn('No se encontraron productos disponibles');
                }
            } catch (error) {
                console.error('Error al cargar productos:', error);
                productosDisponibles = [];
                Swal.fire('Error', 'No se pudieron cargar los productos disponibles: ' + (error.response?.data?.message || error.message), 'error');
            }
        }

        // Abrir modal
        document.getElementById('btn-agregar-producto').addEventListener('click', function() {
            if (!origenActual) {
                Swal.fire('Atención', 'Primero selecciona el origen', 'warning');
                return;
            }

            const modal = document.getElementById('modal-agregar-producto');
            const selectProducto = document.getElementById('modal-producto');
            
            selectProducto.innerHTML = '<option value="">Seleccionar producto...</option>';
            
            console.log('Productos disponibles al abrir modal:', productosDisponibles);
            
            if (productosDisponibles.length === 0) {
                selectProducto.innerHTML += '<option value="" disabled>No hay productos disponibles</option>';
                Swal.fire('Información', 'No hay productos disponibles en el origen seleccionado', 'info');
            } else {
                productosDisponibles.forEach(producto => {
                    // No mostrar productos ya agregados
                    if (!productosSeleccionados.find(p => p.id === producto.id)) {
                        selectProducto.innerHTML += `<option value="${producto.id}" data-stock="${producto.stock_disponible}">${producto.codigo} - ${producto.descripcion}</option>`;
                    }
                });
            }

            modal.classList.remove('hidden');
        });

        function cerrarModalProducto() {
            document.getElementById('modal-agregar-producto').classList.add('hidden');
            document.getElementById('modal-producto').value = '';
            document.getElementById('modal-cantidad').value = '';
            document.getElementById('modal-nota').value = '';
            document.getElementById('modal-stock-info').classList.add('hidden');
        }

        // Mostrar stock cuando se selecciona producto
        document.getElementById('modal-producto').addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            const stock = option.dataset.stock;
            
            if (stock) {
                document.getElementById('modal-stock-disponible').textContent = stock;
                document.getElementById('modal-stock-info').classList.remove('hidden');
            } else {
                document.getElementById('modal-stock-info').classList.add('hidden');
            }
        });

        function confirmarAgregarProducto() {
            const productoId = document.getElementById('modal-producto').value;
            const cantidad = parseFloat(document.getElementById('modal-cantidad').value);
            const nota = document.getElementById('modal-nota').value;

            if (!productoId) {
                Swal.fire('Atención', 'Selecciona un producto', 'warning');
                return;
            }

            if (!cantidad || cantidad <= 0) {
                Swal.fire('Atención', 'Ingresa una cantidad válida', 'warning');
                return;
            }

            const producto = productosDisponibles.find(p => p.id == productoId);
            
            if (cantidad > producto.stock_disponible) {
                Swal.fire('Atención', `La cantidad excede el stock disponible (${producto.stock_disponible})`, 'warning');
                return;
            }

            productosSeleccionados.push({
                id: producto.id,
                codigo: producto.codigo,
                descripcion: producto.descripcion,
                cantidad: cantidad,
                nota: nota,
                stock_disponible: producto.stock_disponible
            });

            actualizarListaProductos();
            actualizarResumen();
            cerrarModalProducto();
        }

        function actualizarListaProductos() {
            const container = document.getElementById('productos-container');
            const emptyState = document.getElementById('empty-state');
            
            if (productosSeleccionados.length === 0) {
                container.innerHTML = '';
                emptyState.classList.remove('hidden');
                return;
            }

            emptyState.classList.add('hidden');
            container.innerHTML = '';

            productosSeleccionados.forEach((producto, index) => {
                const div = document.createElement('div');
                div.className = 'bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-4';
                div.innerHTML = `
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900 dark:text-white">${producto.codigo} - ${producto.descripcion}</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                Cantidad: <strong>${producto.cantidad}</strong> | 
                                Stock disponible: <span class="text-blue-600 dark:text-blue-400">${producto.stock_disponible}</span>
                            </p>
                            ${producto.nota ? `<p class="text-sm text-gray-500 dark:text-gray-500 mt-1 italic">${producto.nota}</p>` : ''}
                        </div>
                        <button type="button" onclick="eliminarProducto(${index})" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                `;
                container.appendChild(div);
            });
        }

        function eliminarProducto(index) {
            productosSeleccionados.splice(index, 1);
            actualizarListaProductos();
            actualizarResumen();
        }

        function limpiarProductos() {
            productosSeleccionados = [];
            actualizarListaProductos();
            actualizarResumen();
        }

        function actualizarResumen() {
            const resumen = document.getElementById('resumen-traslado');
            
            if (productosSeleccionados.length === 0) {
                resumen.classList.add('hidden');
                return;
            }

            resumen.classList.remove('hidden');
            document.getElementById('total-items').textContent = productosSeleccionados.length;
            document.getElementById('cantidad-total').textContent = productosSeleccionados.reduce((sum, p) => sum + p.cantidad, 0).toFixed(2);
        }

        // Enviar formulario
        document.getElementById('form-traslado-multiple').addEventListener('submit', async function(e) {
            e.preventDefault();

            if (productosSeleccionados.length === 0) {
                Swal.fire('Atención', 'Agrega al menos un producto', 'warning');
                return;
            }

            const tipo = document.querySelector('input[name="tipo_traslado"]:checked').value;
            const origen = document.getElementById('select-origen').value;
            const destino = document.getElementById('select-destino').value;
            const notas = document.getElementById('notas').value;

            if (!origen || !destino) {
                Swal.fire('Atención', 'Completa origen y destino', 'warning');
                return;
            }

            const formData = {
                tipo_traslado: tipo,
                notas: notas,
                items: productosSeleccionados.map(p => ({
                    business_product_id: p.id,
                    cantidad: p.cantidad,
                    nota: p.nota
                }))
            };

            if (tipo === 'branch_to_pos') {
                formData.sucursal_origen_id = origen;
                formData.punto_venta_destino_id = destino;
            } else if (tipo === 'pos_to_branch') {
                formData.punto_venta_origen_id = origen;
                formData.sucursal_destino_id = destino;
            } else if (tipo === 'pos_to_pos') {
                formData.punto_venta_origen_id = origen;
                formData.punto_venta_destino_id = destino;
            }

            try {
                const response = await axios.post('{{ route("business.inventory.transfers.store") }}', formData);
                
                if (response.data.success) {
                    await Swal.fire('Éxito', response.data.message, 'success');
                    window.location.href = '{{ route("business.inventory.transfers.index") }}';
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error', error.response?.data?.message || 'Error al crear traslado', 'error');
            }
        });

        // Inicializar
        actualizarUITipoTraslado();
        cargarOrigenesDestinos();
    </script>
    @endpush
@endsection
