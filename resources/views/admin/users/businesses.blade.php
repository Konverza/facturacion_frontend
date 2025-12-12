@extends('layouts.auth-template')
@section('title', 'Negocios Asociados')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full justify-between">
            <h1 class="text-4xl font-bold text-primary-500 dark:text-primary-300">
                Negocios Asociados
            </h1>
        </div>
        <div class="mt-4">
            <div class="mb-4 flex w-full flex-col gap-4 sm:flex-row">
                <div class="flex-[4]">
                    <x-input type="text" placeholder="Buscar negocio" class="w-full" icon="search" id="input-search-data" />
                </div>
                <div class="flex-1">
                    <x-button type="button" icon="plus" typeButton="primary" text="Asociar negocio" class="w-full"
                        data-modal-target="new-association" data-modal-toggle="new-association" />
                </div>
            </div>
            <x-table id="table-data">
                <x-slot name="thead">
                    <x-tr>
                        <x-th class="w-10">#</x-th>
                        <x-th>Negocio</x-th>
                        <x-th>Punto de Venta predeterminado</x-th>
                        <x-th>¿Solo ver DTEs de ese punto de venta?</x-th>
                        <x-th>¿Seleccionar Sucursal para ver?</x-th>
                        <x-th>Acciones</x-th>
                    </x-tr>
                </x-slot>
                <x-slot name="tbody">
                    @forelse ($user->businesses as $business)
                        <x-tr>
                            <x-td>{{ $loop->iteration }}</x-td>
                            <x-td>
                                {{ $business->business->nombre }}
                            </x-td>
                            <x-td>{{ $business->default_pos->nombre ?? 'N/A' }}</x-td>
                            <x-td>
                                @if ($business->only_default_pos)
                                    <span
                                        class="inline-block px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded">Sí</span>
                                @else
                                    <span
                                        class="inline-block px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded">No</span>
                                @endif
                            </x-td>
                            <x-td>
                                @if ($business->branch_selector)
                                    <span
                                        class="inline-block px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded">Sí</span>
                                @else
                                    <span
                                        class="inline-block px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded">No</span>
                                @endif
                            </x-td>
                            <x-td>
                                <x-button type="button" icon="arrow-down" typeButton="primary" text="Acciones"
                                    class="show-options" data-target="#options-businesses-{{ $business->id }}"
                                    size="small" />
                                <div class="options absolute right-0 top-0 z-10 mt-1 hidden w-max rounded-lg border border-gray-300 bg-white p-2 dark:border-gray-800 dark:bg-gray-950"
                                    id="options-businesses-{{ $business->id }}">
                                    <ul class="flex flex-col text-xs">
                                        <li>
                                            <button type="button" 
                                                data-url="{{ Route('admin.users.get_business_json', [$user->id, $business->id]) }}"
                                                data-action="{{ Route('admin.users.update_business', [$user->id, $business->id]) }}" 
                                                data-business-id="{{ $business->id }}"
                                                class="btn-edit-association flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                <x-icon icon="pencil" class="h-4 w-4" />
                                                Editar Asociación
                                            </button>
                                        </li>
                                        <li>
                                            <form
                                                action="{{ Route('admin.users.delete_business', [$user->id, $business->id]) }}"
                                                method="POST" id="form-delete-business-user-{{ $business->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    data-form="form-delete-business-user-{{ $business->id }}"
                                                    data-modal-target="deleteModal" data-modal-toggle="deleteModal"
                                                    class="buttonDelete flex w-full items-center gap-1 rounded-lg px-2 py-2 text-red-500 hover:bg-red-100 dark:text-red-500 dark:hover:bg-red-950/30">
                                                    <x-icon icon="trash" class="h-4 w-4" />
                                                    Eliminar
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </x-td>
                        </x-tr>
                    @empty
                        <x-tr>
                            <x-td colspan="5" class="text-center">No hay negocios asociados.</x-td>
                        </x-tr>
                    @endforelse
                </x-slot>
            </x-table>
        </div>
    </section>

    {{-- Modal new association --}}
    <div id="new-association" tabindex="-1" aria-hidden="true"
        class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
        <div class="relative max-h-full w-full max-w-xl p-4">
            <form action="{{ Route('admin.users.store_business', $user->id) }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                <!-- Modal content -->
                <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                    <!-- Modal header -->
                    <div
                        class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800 md:p-5">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Asociar Negocio
                        </h3>
                        <button type="button"
                            class="ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                            data-modal-hide="new-association">
                            <x-icon icon="x" class="h-5 w-5" />
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4">
                        <div class="flex flex-col gap-4 mb-4">
                            <x-select label="Negocio" name="business_id" id="business_id" placeholder="Seleccionar negocio"
                                :options="$businesses->pluck('nombre', 'id')" 
                                data-action="{{Route('admin.sucursales.json')}}"/>
                        </div>
                        <div class="flex flex-col gap-4 mb-4" id="select-sucursal">
                            <x-select name="sucursal" label="Sucursal" id="sucursal" required :options="[
                                'Seleccione una Sucursal' => 'Seleccione una Sucursal',
                            ]" />
                        </div>
                        <div class="flex flex-col gap-4 mb-4" id="select-punto-venta">
                            <x-select name="pos" label="Punto de Venta" id="pos" required :options="[
                                'Seleccione un Punto de Venta' => 'Seleccione un Punto de Venta',
                            ]" />
                        </div>
                        <div class="mt-4">
                            <x-input type="toggle" name="only_default_pos" label="¿Solo ver DTEs de ese punto de venta?"
                                value="1" id="only_default_pos" />
                        </div>
                        <div class="mt-4">
                            <x-input type="toggle" name="branch_selector" label="¿Seleccionar Sucursal para ver?"
                                value="1" id="branch_selector" />
                        </div>
                    </div>
                    <!-- Modal footer -->
                    <div
                        class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800 md:p-5">
                        <x-button type="button" text="Cancelar" icon="x" typeButton="secondary"
                            data-modal-hide="new-association" />
                        <x-button type="submit" text="Guardar" icon="device-floppy" typeButton="primary" />
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal edit association --}}
    <div id="edit-association" tabindex="-1" aria-hidden="true"
        class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
        <div class="relative max-h-full w-full max-w-xl p-4">
            <form action="" method="POST" id="form-edit-association">
                @csrf
                @method('PUT')
                <!-- Modal content -->
                <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                    <!-- Modal header -->
                    <div
                        class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800 md:p-5">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Editar Asociación de Negocio
                        </h3>
                        <button type="button"
                            class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                            data-target="#edit-association">
                            <x-icon icon="x" class="h-5 w-5" />
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4">
                        <div class="flex flex-col gap-4 mb-4">
                            <label class="mb-1 block text-sm font-medium text-gray-500 dark:text-gray-300">
                                Negocio
                            </label>
                            <input type="text" id="edit_business_name" readonly
                                class="w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-white cursor-not-allowed" />
                            <input type="hidden" name="business_id" id="edit_business_id_input" />
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                El negocio no puede ser modificado. Para cambiar el negocio, elimina esta asociación y crea una nueva.
                            </p>
                        </div>
                        <div class="flex flex-col gap-4 mb-4" id="edit-select-sucursal">
                            <x-select name="sucursal" label="Sucursal" id="edit_sucursal" required 
                                data-action="{{Route('admin.sucursales.json')}}"
                                :options="[
                                'Seleccione una Sucursal' => 'Seleccione una Sucursal',
                            ]" />
                        </div>
                        <div class="flex flex-col gap-4 mb-4" id="edit-select-punto-venta">
                            <x-select name="pos" label="Punto de Venta" id="edit_pos" required :options="[
                                'Seleccione un Punto de Venta' => 'Seleccione un Punto de Venta',
                            ]" />
                        </div>
                        <div class="mt-4">
                            <x-input type="toggle" name="only_default_pos" label="¿Solo ver DTEs de ese punto de venta?"
                                value="1" id="edit_only_default_pos" />
                        </div>
                        <div class="mt-4">
                            <x-input type="toggle" name="branch_selector" label="¿Seleccionar Sucursal para ver?"
                                value="1" id="edit_branch_selector" />
                        </div>
                    </div>
                    <!-- Modal footer -->
                    <div
                        class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800 md:p-5">
                        <x-button type="button" text="Cancelar" icon="x" typeButton="secondary"
                            data-target="#edit-association" class="hide-modal" />
                        <x-button type="submit" text="Actualizar" icon="pencil" typeButton="primary" />
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal delete association --}}
    <x-delete-modal modalId="deleteModal" title="¿Eliminar Asociación?" message="No podrás recuperar este registro" />
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            console.log('Script de edición de asociaciones cargado');
            
            // Manejar clic en botón de editar asociación
            $(document).on('click', '.btn-edit-association', function(e) {
                e.preventDefault();
                console.log('Botón de editar clickeado');
                
                const $button = $(this);
                const url = $button.data('url');
                const action = $button.data('action');
                
                console.log('URL:', url);
                console.log('Action:', action);
                
                if (!url || !action) {
                    console.error('URL o Action no definidos');
                    alert('Error: No se pudo obtener la información necesaria');
                    return;
                }
                
                // Mostrar el modal inmediatamente para verificar que funciona
                $('#edit-association').removeClass('hidden').addClass('flex').css('display', 'flex');
                console.log('Modal mostrado');
                
                // Hacer petición AJAX para obtener los datos de la asociación
                $.ajax({
                    url: url,
                    type: 'GET',
                    beforeSend: function() {
                        console.log('Iniciando petición AJAX...');
                    },
                    success: function(response) {
                        console.log('Respuesta del servidor:', response);
                        
                        // Función auxiliar para actualizar un select personalizado
                        function updateCustomSelect(selectId, value) {
                            console.log(`Intentando actualizar select ${selectId} con valor:`, value, typeof value);
                            
                            const $input = $('#' + selectId);
                            const $selectedText = $('#' + selectId + '_selected');
                            const $selectContainer = $input.closest('div').find('.selectOptions');
                            
                            // Buscar la opción comparando como strings
                            let $option = null;
                            $selectContainer.find('.itemOption').each(function() {
                                const optionValue = $(this).data('value');
                                if (String(optionValue) === String(value)) {
                                    $option = $(this);
                                    return false; // break
                                }
                            });
                            
                            console.log('Opción encontrada:', $option ? $option.length : 0, $option ? $option.html() : 'N/A');
                            
                            if ($option && $option.length && value) {
                                $input.val(value).trigger('Changed');
                                $selectedText.html($option.html());
                                console.log(`✓ Select ${selectId} actualizado exitosamente`);
                            } else {
                                console.warn(`✗ No se pudo actualizar ${selectId}. Valor buscado: ${value}`);
                            }
                        }
                        
                        // Llenar el input hidden y el texto del negocio
                        console.log('=== Actualizando negocio ===');
                        $('#edit_business_id_input').val(response.business_id);
                        
                        // Buscar el nombre del negocio en las opciones del select de creación
                        const businessOption = $('#business_id').closest('div').find(`.selectOptions .itemOption[data-value="${response.business_id}"]`);
                        const businessName = businessOption.length ? businessOption.text().trim() : 'Negocio ID: ' + response.business_id;
                        $('#edit_business_name').val(businessName);
                        console.log('Negocio establecido:', businessName);
                        
                        // Cargar las sucursales
                        $.ajax({
                            url: "{{ Route('admin.sucursales.json') }}",
                            type: 'GET',
                            data: { id: response.business_id },
                            success: function(sucursalesResponse) {
                                console.log('=== Sucursales cargadas ===');
                                $('#edit-select-sucursal').html(sucursalesResponse.html);
                                
                                // Esperar a que el DOM se actualice
                                setTimeout(function() {
                                    if (response.sucursal_id) {
                                        console.log('Intentando seleccionar sucursal:', response.sucursal_id);
                                        updateCustomSelect('edit_sucursal', response.sucursal_id);
                                        
                                        // Disparar el evento Changed para que admin.js cargue los puntos de venta
                                        $('#edit_sucursal').trigger('Changed');
                                        
                                        // Esperar a que se carguen los puntos de venta
                                        setTimeout(function() {
                                            if (response.default_pos_id) {
                                                console.log('Intentando seleccionar punto de venta:', response.default_pos_id);
                                                updateCustomSelect('edit_pos', response.default_pos_id);
                                            }
                                        }, 300);
                                    }
                                }, 200);
                            }
                        });
                        
                        // Remover el código antiguo del MutationObserver
                        /*
                        // Escuchar cuando se carguen las sucursales para seleccionar la correcta
                        const sucursalObserver = new MutationObserver(function(mutations) {
                            mutations.forEach(function(mutation) {
                                if (mutation.addedNodes.length > 0) {
                                    console.log('=== Sucursales cargadas, actualizando sucursal ===');
                                    sucursalObserver.disconnect();
                                    
                                    if (response.sucursal_id) {
                                        updateCustomSelect('edit_sucursal', response.sucursal_id);
                                        
                                        // Escuchar cuando se carguen los puntos de venta
                                        const posObserver = new MutationObserver(function(mutations) {
                                            mutations.forEach(function(mutation) {
                                                if (mutation.addedNodes.length > 0) {
                                                    console.log('=== Puntos de venta cargados, actualizando punto de venta ===');
                                                    posObserver.disconnect();
                                                    
                                                    if (response.default_pos_id) {
                                                        updateCustomSelect('edit_pos', response.default_pos_id);
                                                    }
                                                }
                                            });
                                        });
                                        
                                        const posContainer = document.getElementById('edit-select-punto-venta');
                                        if (posContainer) {
                                            posObserver.observe(posContainer, { childList: true, subtree: true });
                                        }
                                    }
                                }
                            });
                        });
                        
                        */
                        
                        // Establecer los toggles
                        $('#edit_only_default_pos').prop('checked', response.only_default_pos == 1);
                        $('#edit_branch_selector').prop('checked', response.branch_selector == 1);
                        
                        // Actualizar la acción del formulario
                        $('#form-edit-association').attr('action', action);
                    },
                    error: function(xhr) {
                        console.error('Error al cargar los datos:', xhr);
                        console.error('Status:', xhr.status);
                        console.error('Response:', xhr.responseText);
                        alert('Error al cargar los datos de la asociación: ' + (xhr.responseJSON?.error || xhr.statusText));
                    }
                });
            });

            // Cerrar modal
            $(document).on('click', '.hide-modal', function(e) {
                e.preventDefault();
                const target = $(this).data('target');
                console.log('Cerrando modal:', target);
                $(target).addClass('hidden').removeClass('flex').css('display', 'none');
            });

            // Cerrar modal al hacer clic en el fondo oscuro
            $(document).on('click', '#edit-association', function(e) {
                if (e.target === this) {
                    $(this).addClass('hidden').removeClass('flex').css('display', 'none');
                }
            });
        });
    </script>
@endpush
