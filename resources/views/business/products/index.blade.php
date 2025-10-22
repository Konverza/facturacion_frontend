@extends('layouts.auth-template')
@section('title', 'Productos')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full justify-between">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Productos
            </h1>
        </div>
        <livewire:business.tables.products />
        <x-delete-modal modalId="deleteModal" title="¿Estás seguro de eliminar el producto?"
            message="No podrás recuperar este registro" />

        <div id="modal-add-stock" tabindex="-1" aria-hidden="true"
            class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
            <div class="relative max-h-full w-full max-w-lg p-4">
                <!-- Modal content -->
                <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                    <div class="flex flex-col">
                        <!-- Modal header -->
                        <form action="{{ Route('business.products.add-stock') }}" method="POST" id="form-add-stock">
                            @csrf
                            <div
                                class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    Agregar stock
                                </h3>
                                <button type="button"
                                    class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                                    data-target="#modal-add-stock">
                                    <x-icon icon="x" class="h-5 w-5" />
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="flex flex-col gap-4 p-4">
                                <input type="hidden" name="id" id="product-id">
                                <input type="hidden" name="sucursal_id" id="sucursal-id-add">
                                
                                @php
                                    $selectedSucursalId = null;
                                    $sucursalNombre = 'Sucursal actual';
                                    
                                    // Intentar obtener la sucursal del componente Livewire
                                    try {
                                        $component = app(\App\Livewire\Business\Tables\Products::class);
                                        if ($component && property_exists($component, 'selectedSucursalId')) {
                                            $selectedSucursalId = $component->selectedSucursalId;
                                            if ($selectedSucursalId && isset($component->availableSucursales[$selectedSucursalId])) {
                                                $sucursalNombre = $component->availableSucursales[$selectedSucursalId];
                                            }
                                        }
                                    } catch (\Exception $e) {
                                        // Silenciar error
                                    }
                                @endphp
                                
                                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                    <p class="text-sm text-blue-700 dark:text-blue-300">
                                        <x-icon icon="map-pin" class="inline w-4 h-4" /> 
                                        <strong>Sucursal:</strong> <span id="sucursal-nombre-add">{{ $sucursalNombre }}</span>
                                    </p>
                                </div>
                                
                                <x-input type="number" icon="box" placeholder="Ingresar cantidad" name="cantidad"
                                    required label="Cantidad" min="1" />
                                <x-input type="textarea" placeholder="Ingresa la descripción" name="descripcion"
                                    label="Descripción" required />
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                                <x-button type="button" class="hide-modal" text="Cancelar" icon="x"
                                    typeButton="secondary" data-target="#modal-add-stock" />
                                <x-button type="submit" text="Agregar" icon="plus" typeButton="primary" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="modal-remove-stock" tabindex="-1" aria-hidden="true"
            class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
            <div class="relative max-h-full w-full max-w-lg p-4">
                <!-- Modal content -->
                <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                    <div class="flex flex-col">
                        <!-- Modal header -->
                        <form action="{{ Route('business.products.remove-stock') }}" method="POST" id="form-remove-stock">
                            @csrf
                            <div
                                class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    Disminuir stock
                                </h3>
                                <button type="button"
                                    class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                                    data-target="#modal-remove-stock">
                                    <x-icon icon="x" class="h-5 w-5" />
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="flex flex-col gap-4 p-4">
                                <input type="hidden" name="id" id="product-remove-id">
                                <input type="hidden" name="sucursal_id" id="sucursal-id-remove">
                                
                                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                    <p class="text-sm text-blue-700 dark:text-blue-300">
                                        <x-icon icon="map-pin" class="inline w-4 h-4" /> 
                                        <strong>Sucursal:</strong> <span id="sucursal-nombre-remove">{{ $sucursalNombre ?? 'Sucursal actual' }}</span>
                                    </p>
                                </div>
                                
                                <x-input type="number" icon="box" placeholder="Ingresar cantidad" name="cantidad"
                                    required label="Cantidad" min="1" />
                                <x-input type="textarea" placeholder="Ingresa la descripción" name="descripcion"
                                    label="Descripción" required />
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                                <x-button type="button" class="hide-modal" text="Cancelar" icon="x"
                                    typeButton="secondary" data-target="#modal-remove-stock" />
                                <x-button type="submit" text="Disminuir" icon="minus" typeButton="primary" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Trasladar Stock -->
        <div id="modal-transfer-stock" tabindex="-1" aria-hidden="true"
            class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
            <div class="relative max-h-full w-full max-w-lg p-4">
                <!-- Modal content -->
                <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                    <div class="flex flex-col">
                        <!-- Modal header -->
                        <form action="{{ Route('business.products.transfer-stock') }}" method="POST" id="form-transfer-stock"
                            class="submit-form">
                            @csrf
                            <div
                                class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    <x-icon icon="arrows-transfer-left-right" class="inline w-5 h-5" />
                                    Trasladar Stock
                                </h3>
                                <button type="button"
                                    class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                                    data-target="#modal-transfer-stock">
                                    <x-icon icon="x" class="h-5 w-5" />
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="flex flex-col gap-4 p-4">
                                <input type="hidden" name="id" id="product-transfer-id">
                                <input type="hidden" name="sucursal_origen_id" id="sucursal-origen-id">
                                
                                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                    <p class="text-sm text-blue-700 dark:text-blue-300">
                                        <x-icon icon="map-pin-filled" class="inline w-4 h-4" /> 
                                        <strong>Sucursal Origen:</strong> <span id="sucursal-origen-nombre">Sucursal actual</span>
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        <x-icon icon="building-store" class="inline w-4 h-4" /> Sucursal Destino
                                    </label>
                                    <select name="sucursal_destino_id" id="sucursal-destino-id" required
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                        <option value="">Seleccione sucursal destino</option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1" id="stock-destino-info"></p>
                                </div>
                                
                                <x-input type="number" icon="box" placeholder="Ingresar cantidad" name="cantidad"
                                    required label="Cantidad a Trasladar" min="1" id="cantidad-transfer" />
                                
                                <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                                    <p class="text-xs text-yellow-700 dark:text-yellow-300">
                                        <x-icon icon="alert-triangle" class="inline w-4 h-4" /> 
                                        Stock disponible en origen: <strong id="stock-disponible-origen">0</strong> unidades
                                    </p>
                                </div>

                                <x-input type="textarea" placeholder="Notas opcionales del traslado" name="notas"
                                    label="Notas (Opcional)" rows="2" />
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                                <x-button type="button" class="hide-modal" text="Cancelar" icon="x"
                                    typeButton="secondary" data-target="#modal-transfer-stock" />
                                <x-button type="submit" text="Trasladar" icon="arrows-transfer-left-right" typeButton="primary" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="modal-import" tabindex="-1" aria-hidden="true"
            class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
            <div class="relative max-h-full w-full max-w-lg p-4">
                <!-- Modal content -->
                <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                    <div class="flex flex-col">
                        <!-- Modal header -->
                        <form action="{{ Route('business.products.import') }}" method="POST" id="form-import"
                            enctype="multipart/form-data">
                            @csrf
                            <div
                                class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    Importar Productos
                                </h3>
                                <button type="button"
                                    class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                                    data-target="#modal-import">
                                    <x-icon icon="x" class="h-5 w-5" />
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="flex flex-col gap-4 p-4">
                                <div
                                    class="my-2 rounded-lg border border-dashed border-blue-500 bg-blue-100 p-4 text-blue-500 dark:bg-blue-950/30">
                                    <b>Nota: </b> Debe utilizar un archivo en formato <b>.xlsx</b> disponible en
                                    <a href="{{ $business->show_special_prices == 1 ? url('templates/importacion_productos_completo.xlsx') : url('templates/importacion_productos.xlsx') }}" target="_blank"
                                        class="text-blue-600 underline dark:text-blue-400">este enlace</a> para
                                    importar productos.
                                </div>
                                <div
                                    class="mb-2 rounded-lg border border-dashed border-yellow-500 bg-yellow-100 p-4 text-yellow-500 dark:bg-yellow-950/30">
                                    <b>Advertencia: </b> Tome en cuenta que al importar productos, si coincide el código y la descripcion de un producto ya existente, se actualizará el producto con los nuevos datos. Si no existe, se creará un nuevo producto.
                                </div>
                                <x-input type="file" label="Archivo de Productos" name="file" id="file"
                                    accept=".xlsx" maxSize="3072" />
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                                <x-button type="button" class="hide-modal" text="Cancelar" icon="x"
                                    typeButton="secondary" data-target="#modal-import" />
                                <x-button type="submit" text="Importar" icon="cloud-upload" typeButton="primary" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection

@push('scripts')
<script>
    // Función para obtener la sucursal seleccionada
    function getSucursalSeleccionada() {
        // Primero buscar si existe un selector de sucursal (para usuarios con branch_selector)
        const selector = document.getElementById('sucursal-selector');
        if (selector && selector.value) {
            return {
                id: selector.value,
                nombre: selector.options[selector.selectedIndex]?.text || 'Sucursal actual'
            };
        }
        
        // Si no hay selector, buscar en el banner con data attributes (sucursal por defecto)
        const banner = document.querySelector('[data-sucursal-id]');
        if (banner) {
            const sucursalId = banner.getAttribute('data-sucursal-id');
            const nombre = banner.getAttribute('data-sucursal-nombre');
            
            if (sucursalId) {
                return {
                    id: sucursalId,
                    nombre: nombre || 'Sucursal actual'
                };
            }
        }
        
        return null;
    }

    // Cuando se abre el modal de agregar stock
    $(document).on('click', '.btn-add-stock', function() {
        const productId = $(this).data('id');
        $('#product-id').val(productId);
        
        // Obtener y asignar la sucursal
        const sucursal = getSucursalSeleccionada();
        if (sucursal) {
            $('#sucursal-id-add').val(sucursal.id);
            $('#sucursal-nombre-add').text(sucursal.nombre);
        } else {
            // Si no hay sucursal seleccionada, mostrar error
            alert('Por favor, seleccione una sucursal antes de agregar stock.');
            $('#modal-add-stock').addClass('hidden');
            return false;
        }
    });

    // Cuando se abre el modal de disminuir stock
    $(document).on('click', '.btn-remove-stock', function() {
        const productId = $(this).data('id');
        $('#product-remove-id').val(productId);
        
        // Obtener y asignar la sucursal
        const sucursal = getSucursalSeleccionada();
        if (sucursal) {
            $('#sucursal-id-remove').val(sucursal.id);
            $('#sucursal-nombre-remove').text(sucursal.nombre);
        } else {
            // Si no hay sucursal seleccionada, mostrar error
            alert('Por favor, seleccione una sucursal antes de disminuir stock.');
            $('#modal-remove-stock').addClass('hidden');
            return false;
        }
    });

    // Validar antes de enviar el formulario de agregar stock
    $('#form-add-stock').on('submit', function(e) {
        const sucursalId = $('#sucursal-id-add').val();
        if (!sucursalId) {
            e.preventDefault();
            alert('Error: No se ha seleccionado una sucursal. Por favor, recargue la página e intente nuevamente.');
            return false;
        }
    });

    // Validar antes de enviar el formulario de disminuir stock
    $('#form-remove-stock').on('submit', function(e) {
        const sucursalId = $('#sucursal-id-remove').val();
        if (!sucursalId) {
            e.preventDefault();
            alert('Error: No se ha seleccionado una sucursal. Por favor, recargue la página e intente nuevamente.');
            return false;
        }
    });

    // Cuando se abre el modal de trasladar stock
    $(document).on('click', '.btn-transfer-stock', function() {
        const productId = $(this).data('id');
        $('#product-transfer-id').val(productId);
        
        // Obtener sucursal de origen
        const sucursalOrigen = getSucursalSeleccionada();
        if (!sucursalOrigen) {
            alert('Por favor, seleccione una sucursal antes de trasladar stock.');
            $('#modal-transfer-stock').addClass('hidden');
            return false;
        }
        
        $('#sucursal-origen-id').val(sucursalOrigen.id);
        $('#sucursal-origen-nombre').text(sucursalOrigen.nombre);
        
        // Obtener información del producto y cargar sucursales disponibles
        $.ajax({
            url: '/business/products/' + productId + '/branch-info',
            method: 'GET',
            data: { sucursal_id: sucursalOrigen.id },
            success: function(response) {
                if (response.success) {
                    // Actualizar stock disponible
                    $('#stock-disponible-origen').text(response.stock_origen);
                    $('#cantidad-transfer').attr('max', response.stock_origen);
                    
                    // Cargar sucursales destino (todas menos la origen)
                    const select = $('#sucursal-destino-id');
                    select.empty();
                    select.append('<option value="">Seleccione sucursal destino</option>');
                    
                    response.sucursales_disponibles.forEach(function(sucursal) {
                        if (sucursal.id != sucursalOrigen.id) {
                            select.append(`<option value="${sucursal.id}">${sucursal.nombre}</option>`);
                        }
                    });
                } else {
                    alert(response.message || 'Error al cargar información del producto');
                    $('#modal-transfer-stock').addClass('hidden');
                }
            },
            error: function() {
                alert('Error al cargar la información del producto');
                $('#modal-transfer-stock').addClass('hidden');
            }
        });
    });

    // Al cambiar la sucursal destino, mostrar stock actual
    $('#sucursal-destino-id').on('change', function() {
        const productId = $('#product-transfer-id').val();
        const sucursalDestinoId = $(this).val();
        
        if (sucursalDestinoId && productId) {
            $.ajax({
                url: '/business/products/' + productId + '/branch-stock',
                method: 'GET',
                data: { sucursal_id: sucursalDestinoId },
                success: function(response) {
                    if (response.success) {
                        $('#stock-destino-info').text(`Stock actual en destino: ${response.stock} unidades`);
                    }
                }
            });
        } else {
            $('#stock-destino-info').text('');
        }
    });

    // Validar antes de enviar el formulario de traslado
    $('#form-transfer-stock').on('submit', function(e) {
        e.preventDefault();
        
        const sucursalOrigenId = $('#sucursal-origen-id').val();
        const sucursalDestinoId = $('#sucursal-destino-id').val();
        const cantidad = parseFloat($('#cantidad-transfer').val());
        const stockDisponible = parseFloat($('#stock-disponible-origen').text());
        
        // Validaciones
        if (!sucursalOrigenId) {
            alert('Error: No se ha seleccionado una sucursal de origen.');
            return false;
        }
        
        if (!sucursalDestinoId) {
            alert('Por favor, seleccione una sucursal de destino.');
            return false;
        }
        
        if (sucursalOrigenId === sucursalDestinoId) {
            alert('La sucursal de origen y destino no pueden ser la misma.');
            return false;
        }
        
        if (cantidad <= 0) {
            alert('La cantidad debe ser mayor a 0.');
            return false;
        }
        
        if (cantidad > stockDisponible) {
            alert(`Stock insuficiente. Solo hay ${stockDisponible} unidades disponibles en la sucursal de origen.`);
            return false;
        }
        
        // Si todas las validaciones pasan, enviar el formulario
        const formData = $(this).serialize();
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert(response.message || 'Traslado realizado exitosamente');
                    $('#modal-transfer-stock').addClass('hidden');
                    location.reload(); // Recargar para ver los cambios
                } else {
                    alert(response.message || 'Error al realizar el traslado');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                alert(response?.message || 'Error al realizar el traslado');
            }
        });
    });
</script>
@endpush
