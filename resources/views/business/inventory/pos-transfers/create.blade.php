@extends('layouts.auth-template')
@section('title', 'Crear Traslado')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                    Crear Traslado
                </h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Trasladar productos entre sucursales y puntos de venta
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
            <form id="form-traslado" class="p-6 space-y-6">
                @csrf

                <!-- Tipo de Traslado -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        <x-icon icon="arrows-right-left" class="inline w-4 h-4" />
                        Tipo de Traslado
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="tipo-card relative flex cursor-pointer rounded-lg border-2 border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm focus:outline-none hover:border-primary-300 dark:hover:border-primary-700 transition-all duration-200">
                            <input type="radio" name="tipo_traslado" value="branch_to_pos" class="sr-only" checked>
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="block text-sm font-medium text-gray-900 dark:text-white">
                                        <x-icon icon="arrow-right" class="inline w-4 h-4" />
                                        Sucursal → Punto de Venta
                                    </span>
                                    <span class="mt-1 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                        Asignar productos a un punto de venta
                                    </span>
                                </span>
                            </span>
                            <x-icon icon="check-circle" class="h-5 w-5 text-primary-600 dark:text-primary-500 tipo-check hidden" />
                        </label>

                        <label class="tipo-card relative flex cursor-pointer rounded-lg border-2 border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm focus:outline-none hover:border-primary-300 dark:hover:border-primary-700 transition-all duration-200">
                            <input type="radio" name="tipo_traslado" value="pos_to_branch" class="sr-only">
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="block text-sm font-medium text-gray-900 dark:text-white">
                                        <x-icon icon="arrow-left" class="inline w-4 h-4" />
                                        Punto de Venta → Sucursal
                                    </span>
                                    <span class="mt-1 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                        Devolver productos a la sucursal
                                    </span>
                                </span>
                            </span>
                            <x-icon icon="check-circle" class="h-5 w-5 text-primary-600 dark:text-primary-500 tipo-check hidden" />
                        </label>

                        <label class="tipo-card relative flex cursor-pointer rounded-lg border-2 border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm focus:outline-none hover:border-primary-300 dark:hover:border-primary-700 transition-all duration-200">
                            <input type="radio" name="tipo_traslado" value="pos_to_pos" class="sr-only">
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="block text-sm font-medium text-gray-900 dark:text-white">
                                        <x-icon icon="arrows-right-left" class="inline w-4 h-4" />
                                        Punto de Venta → Punto de Venta
                                    </span>
                                    <span class="mt-1 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                        Trasladar entre puntos de venta
                                    </span>
                                </span>
                            </span>
                            <x-icon icon="check-circle" class="h-5 w-5 text-primary-600 dark:text-primary-500 tipo-check hidden" />
                        </label>
                    </div>
                </div>

                <!-- Origen -->
                <div id="origen-container">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        <x-icon icon="map-pin" class="inline w-4 h-4" />
                        Origen
                    </label>
                    <select id="select-origen" name="origen_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-900 dark:border-gray-700 dark:text-white" required>
                        <option value="">Seleccionar origen...</option>
                        <!-- Se llena dinámicamente -->
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
                        <!-- Se llena dinámicamente -->
                    </select>
                </div>

                <!-- Producto -->
                <div id="producto-container" style="display: none;">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        <x-icon icon="package" class="inline w-4 h-4" />
                        Producto
                    </label>
                    <select id="select-producto" name="business_product_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-900 dark:border-gray-700 dark:text-white" required>
                        <option value="">Seleccionar producto...</option>
                    </select>
                    <div id="stock-info" class="mt-2 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800" style="display: none;">
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            <strong>Stock disponible:</strong> <span id="stock-disponible">0</span> unidades
                        </p>
                    </div>
                </div>

                <!-- Cantidad -->
                <div id="cantidad-container" style="display: none;">
                    <x-input 
                        type="number" 
                        icon="hash" 
                        placeholder="Ingresar cantidad" 
                        name="cantidad"
                        required 
                        label="Cantidad" 
                        min="0.01"
                        step="0.01"
                        id="input-cantidad"
                    />
                </div>

                <!-- Notas -->
                <div id="notas-container" style="display: none;">
                    <x-input 
                        type="textarea" 
                        placeholder="Notas adicionales sobre el traslado" 
                        name="notas"
                        label="Notas (opcional)"
                        id="input-notas"
                    />
                </div>

                <!-- Botones -->
                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-800">
                    <x-button 
                        type="button" 
                        text="Cancelar" 
                        icon="x"
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

    @push('scripts')
    <script>
        const sucursales = @json($sucursales);
        const puntosVenta = @json($puntosVenta);
        let currentTipo = 'branch_to_pos';

        // Manejar cambio de tipo de traslado
        document.querySelectorAll('input[name="tipo_traslado"]').forEach(radio => {
            radio.addEventListener('change', function() {
                currentTipo = this.value;
                updateOriginDestination();
                resetForm();
                
                // Update visual feedback
                updateTipoCards(this);
            });
        });

        // Función para actualizar el estado visual de las tarjetas
        function updateTipoCards(selectedRadio) {
            // Remover estilos de todas las tarjetas
            document.querySelectorAll('.tipo-card').forEach(card => {
                card.classList.remove('border-primary-500', 'dark:border-primary-400', 'bg-primary-50', 'dark:bg-primary-900/20', 'ring-2', 'ring-primary-500', 'dark:ring-primary-400');
                card.classList.add('border-gray-300', 'dark:border-gray-700', 'bg-white', 'dark:bg-gray-900');
                
                // Ocultar todos los checks
                const check = card.querySelector('.tipo-check');
                if (check) {
                    check.classList.add('hidden');
                }
            });
            
            // Agregar estilos a la tarjeta seleccionada
            const selectedCard = selectedRadio.closest('.tipo-card');
            if (selectedCard) {
                selectedCard.classList.remove('border-gray-300', 'dark:border-gray-700', 'bg-white', 'dark:bg-gray-900');
                selectedCard.classList.add('border-primary-500', 'dark:border-primary-400', 'bg-primary-50', 'dark:bg-primary-900/20', 'ring-2', 'ring-primary-500', 'dark:ring-primary-400');
                
                // Mostrar el check de la tarjeta seleccionada
                const checkIcon = selectedCard.querySelector('.tipo-check');
                if (checkIcon) {
                    checkIcon.classList.remove('hidden');
                }
            }
        }

        // Inicializar primera opción
        const firstChecked = document.querySelector('input[name="tipo_traslado"]:checked');
        if (firstChecked) {
            updateTipoCards(firstChecked);
        }

        function updateOriginDestination() {
            const selectOrigen = document.getElementById('select-origen');
            const selectDestino = document.getElementById('select-destino');
            
            selectOrigen.innerHTML = '<option value="">Seleccionar origen...</option>';
            selectDestino.innerHTML = '<option value="">Seleccionar destino...</option>';

            if (currentTipo === 'branch_to_pos') {
                // Origen: Sucursales
                sucursales.forEach(suc => {
                    selectOrigen.innerHTML += `<option value="sucursal-${suc.id}">${suc.nombre}</option>`;
                });
                // Destino: Puntos de Venta
                puntosVenta.forEach(pos => {
                    selectDestino.innerHTML += `<option value="pos-${pos.id}">${pos.nombre} (${pos.sucursal.nombre})</option>`;
                });
            } else if (currentTipo === 'pos_to_branch') {
                // Origen: Puntos de Venta
                puntosVenta.forEach(pos => {
                    selectOrigen.innerHTML += `<option value="pos-${pos.id}">${pos.nombre} (${pos.sucursal.nombre})</option>`;
                });
                // Destino: Sucursales
                sucursales.forEach(suc => {
                    selectDestino.innerHTML += `<option value="sucursal-${suc.id}">${suc.nombre}</option>`;
                });
            } else if (currentTipo === 'pos_to_pos') {
                // Origen y Destino: Puntos de Venta
                puntosVenta.forEach(pos => {
                    const option = `<option value="pos-${pos.id}">${pos.nombre} (${pos.sucursal.nombre})</option>`;
                    selectOrigen.innerHTML += option;
                    selectDestino.innerHTML += option;
                });
            }
        }

        // Cargar productos cuando se selecciona origen
        document.getElementById('select-origen').addEventListener('change', function() {
            const origenValue = this.value;
            if (!origenValue) return;

            const [tipo, id] = origenValue.split('-');
            loadProductos(tipo, id);
        });

        function loadProductos(tipo, id) {
            const tipoParam = tipo === 'sucursal' ? 'branch' : 'pos';
            const baseUrl = '{{ route('business.inventory.transfers.products.available') }}';
            
            fetch(`${baseUrl}?tipo_traslado=${currentTipo}&origen_id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const selectProducto = document.getElementById('select-producto');
                        selectProducto.innerHTML = '<option value="">Seleccionar producto...</option>';
                        
                        data.productos.forEach(prod => {
                            selectProducto.innerHTML += `
                                <option value="${prod.id}" data-stock="${prod.stock_disponible}">
                                    ${prod.codigo} - ${prod.descripcion} (Stock: ${prod.stock_disponible})
                                </option>
                            `;
                        });
                        
                        document.getElementById('producto-container').style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar productos');
                });
        }

        // Mostrar stock disponible cuando se selecciona producto
        document.getElementById('select-producto').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const stock = selectedOption.getAttribute('data-stock');
            
            if (stock) {
                document.getElementById('stock-disponible').textContent = stock;
                document.getElementById('stock-info').style.display = 'block';
                document.getElementById('cantidad-container').style.display = 'block';
                document.getElementById('notas-container').style.display = 'block';
                
                // Establecer max en el input de cantidad
                document.getElementById('input-cantidad').setAttribute('max', stock);
            }
        });

        function resetForm() {
            document.getElementById('select-origen').value = '';
            document.getElementById('select-destino').value = '';
            document.getElementById('select-producto').innerHTML = '<option value="">Seleccionar producto...</option>';
            document.getElementById('input-cantidad').value = '';
            document.getElementById('input-notas').value = '';
            document.getElementById('producto-container').style.display = 'none';
            document.getElementById('cantidad-container').style.display = 'none';
            document.getElementById('notas-container').style.display = 'none';
            document.getElementById('stock-info').style.display = 'none';
        }

        // Submit form
        document.getElementById('form-traslado').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('tipo_traslado', currentTipo);
            formData.append('business_product_id', document.getElementById('select-producto').value);
            formData.append('cantidad', document.getElementById('input-cantidad').value);
            formData.append('notas', document.getElementById('input-notas').value);
            
            // Agregar origen y destino según el tipo
            const origenValue = document.getElementById('select-origen').value;
            const destinoValue = document.getElementById('select-destino').value;
            
            const [tipoOrigen, idOrigen] = origenValue.split('-');
            const [tipoDestino, idDestino] = destinoValue.split('-');
            
            if (tipoOrigen === 'sucursal') {
                formData.append('sucursal_origen_id', idOrigen);
            } else {
                formData.append('punto_venta_origen_id', idOrigen);
            }
            
            if (tipoDestino === 'sucursal') {
                formData.append('sucursal_destino_id', idDestino);
            } else {
                formData.append('punto_venta_destino_id', idDestino);
            }
            
            // Deshabilitar botón
            const btnSubmit = document.getElementById('btn-submit');
            const originalBtnText = btnSubmit.innerHTML;
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<svg class="inline w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3a9 9 0 1 0 9 9" /></svg> Procesando...';
            
            fetch('{{ route('business.inventory.transfers.store') }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Traslado realizado exitosamente');
                    window.location.href = '{{ route('business.inventory.transfers.index') }}';
                } else {
                    alert('Error: ' + data.message);
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = originalBtnText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar el traslado');
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = originalBtnText;
            });
        });

        // Inicializar
        updateOriginDestination();
    </script>
    @endpush
@endsection
