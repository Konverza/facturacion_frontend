@php
    $business_id = Session::get('business') ?? null;
    $business = \App\Models\Business::find($business_id);
@endphp
@extends('layouts.auth-template')
@section('title', 'Nuevo cliente')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full items-center justify-between">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Editar cliente
            </h1>
            <a href="{{ Route('business.customers.index') }}"
                class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
                <x-icon icon="arrow-back" class="size-5" />
                Regresar
            </a>
        </div>
        <div class="mt-4 rounded-lg pb-4">
            <form action="{{ Route('business.customers.update', $customer->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="flex flex-col gap-4 sm:flex-row">
                    <div class="flex-1">
                        <x-select label="Tipo de documento" name="tipo_documento" id="type_document" :options="$tipos_documentos"
                            value="{{ $customer->tipoDocumento }}" selected="{{ $customer->tipoDocumento }}" />
                    </div>
                    <div class="flex-[2]">
                        <x-input type="text" label="Número de documento" name="numero_documento"
                            value="{{ $customer->numDocumento }}" placeholder="Ingresar el número de documento"
                            id="numero_documento" />
                    </div>
                </div>
                <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                    <div class="flex-1">
                        <x-input type="text" label="Nombre / Razón social" name="nombre" value="{{ $customer->nombre }}"
                            placeholder="Ingresa el nombre o razón social" id="nombre" />
                    </div>
                    <div class="flex-1">
                        <x-input type="text" label="NRC" name="nrc" id="nrc" value="{{ $customer->nrc }}" />
                    </div>
                </div>
                <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                    <div class="flex-1">
                        <x-input type="text" label="Nombre comercial" name="nombre_comercial" id="nombre_comercial"
                            placeholder="Ingresar el nombre comercial" value="{{ $customer->nombreComercial }}" />
                    </div>
                    <div class="flex-1">
                        <x-select id="actividad_economica" :options="$actividades_economicas" label="Actividad económica"
                            selected="{{ $customer->codActividad }}" name="actividad_economica"
                            value="{{ old('actividad_economica', $customer->codActividad) }}" />
                    </div>
                </div>
                <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                    <div class="flex-1">
                        <x-select name="department" label="Departamento" id="departamento" name="departamento" required
                            :options="$departamentos" value="{{ old('departamento', $customer->departamento) }}"
                            selected="{{ old('departamento', $customer->departamento) }}"
                            data-action="{{ Route('business.get-municipios') }}" />
                    </div>
                    <div class="flex-1" id="select-municipio">
                        <x-select name="municipio" label="Municipio" id="municipality" required :options="$municipios"
                            value="{{ $customer->municipio }}" selected="{{ $customer->municipio }}" />
                    </div>
                </div>
                <div class="mt-4">
                    <x-input type="textarea" label="Dirección" name="complemento" id="complemento"
                        placeholder="Ingresar la dirección" value="{{ old('complemento', $customer->complemento) }}" />
                </div>
                <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                    <div class="flex-1">
                        <x-input type="text" label="Correo electrónico" icon="email" name="correo" id="correo"
                            placeholder="example@examp.com" value="{{ old('correo', $customer->correo) }}" />
                    </div>
                    <div class="flex-1">
                        <x-input type="text" label="Teléfono" icon="phone" name="telefono" id="telefono"
                            placeholder="XXXX XXXX" value="{{ old('telefono', $customer->telefono) }}" />
                    </div>
                </div>
                @if($business->show_special_prices)
                    <div class="mt-4">
                        <x-input type="checkbox" label="Aplicar precio especial a este cliente" name="special_price" id="special_price" :checked="$customer->special_price" />
                    </div>
                @endif
                @if ($business->has_customer_branches)
                    <div class="mt-4">
                        <x-input type="checkbox" label="Este cliente tiene sucursales" name="use_branches"
                            id="use-branches" :checked="$customer->use_branches" />
                    </div>
                @endif
                <div class="mt-4">
                    @php
                        $checked = $customer->codPais || $customer->tipoPersona ? true : false;
                    @endphp
                    <x-input type="checkbox" label="Rellenar datos de exportación" name="export_data" id="export-data"
                        :checked="$checked" />
                </div>
                <div class="hidden" id="export-data-container">
                    <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                        <div class="flex-1">
                            <x-select label="País" name="codigo_pais" id="codigo_pais" :options="$countries"
                                value="{{ old('codigo_pais', $customer->codPais) }}"
                                selected="{{ old('codigo_pais', $customer->codPais) }}" />
                        </div>
                        <div class="flex-1">
                            <x-select label="Tipo de persona" name="tipo_persona" id="tipo_persona" :options="[
                                '1' => 'Persona natural',
                                '2' => 'Persona jurídica',
                            ]"
                                value="{{ old('tipo_persona', $customer->tipoPersona) }}" :search="false"
                                selected="{{ old('tipo_persona', $customer->tipoPersona) }}" />
                        </div>
                    </div>
                </div>

                {{-- Sección de sucursales --}}
                <div class="{{ $customer->use_branches ? '' : 'hidden' }} mt-4" id="branches-container">
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Sucursales del Cliente</h3>
                            <x-button type="button" typeButton="secondary" text="Agregar Sucursal" 
                                class="text-sm" icon="add" id="add-branch-btn" />
                        </div>
                        <div id="branches-list" class="space-y-3">
                            @foreach($customer->branches as $branch)
                                <div class="branch-item border border-gray-300 dark:border-gray-600 rounded-lg p-4 relative" data-id="{{ $branch->id }}">
                                    <input type="hidden" name="existing_branches[{{ $loop->index }}][id]" value="{{ $branch->id }}">
                                    <button type="button" class="absolute top-2 right-2 text-red-500 hover:text-red-700 remove-branch-btn">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Código de Sucursal <span class="text-red-500">*</span></label>
                                            <input type="text" name="existing_branches[{{ $loop->index }}][branch_code]" 
                                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2.5" 
                                                placeholder="Ej: SUC001" value="{{ $branch->branch_code }}" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre de Sucursal <span class="text-red-500">*</span></label>
                                            <input type="text" name="existing_branches[{{ $loop->index }}][nombre]" 
                                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2.5" 
                                                placeholder="Nombre de la sucursal" value="{{ $branch->nombre }}" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Departamento <span class="text-red-500">*</span></label>
                                            <select name="existing_branches[{{ $loop->index }}][departamento]" 
                                                class="branch-departamento w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2.5" 
                                                data-branch-index="existing-{{ $loop->index }}" required>
                                                <option value="">Seleccione un departamento</option>
                                                @foreach($departamentos as $codigo => $nombre)
                                                    <option value="{{ $codigo }}" {{ $branch->departamento == $codigo ? 'selected' : '' }}>{{ $nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Municipio <span class="text-red-500">*</span></label>
                                            <select name="existing_branches[{{ $loop->index }}][municipio]" 
                                                class="branch-municipio-existing-{{ $loop->index }} w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2.5" 
                                                required>
                                                <option value="">Seleccione un municipio</option>
                                                @if(isset($branchesMunicipios[$branch->id]))
                                                    @foreach($branchesMunicipios[$branch->id] as $codigo => $nombre)
                                                        <option value="{{ $codigo }}" {{ $branch->municipio == $codigo ? 'selected' : '' }}>{{ $nombre }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dirección <span class="text-red-500">*</span></label>
                                            <textarea name="existing_branches[{{ $loop->index }}][complemento]" 
                                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2.5" 
                                                rows="2" placeholder="Dirección completa de la sucursal" required>{{ $branch->complemento }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-center">
                    <x-button type="submit" typeButton="primary" text="Editar cliente" class="w-full sm:w-auto"
                        icon="pencil" />
                </div>
            </form>
        </div>
    </section>

    {{-- Modal de confirmación para eliminar sucursal --}}
    <div id="delete-branch-modal" tabindex="-1" aria-hidden="true"
        class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-900/50 dark:bg-gray-900/80 md:inset-0">
        <div class="relative max-h-full w-full max-w-md p-4">
            <div class="relative rounded-lg bg-white shadow dark:bg-gray-800">
                <button type="button" class="absolute end-2.5 top-3 ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-600 dark:hover:text-white"
                    onclick="closeBranchDeleteModal()">
                    <svg class="h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Cerrar modal</span>
                </button>
                <div class="p-4 text-center md:p-5">
                    <svg class="mx-auto mb-4 h-12 w-12 text-red-600 dark:text-red-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                    <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">
                        ¿Estás seguro de eliminar la sucursal "<span id="branch-name-to-delete" class="font-semibold"></span>"?
                    </h3>
                    <p class="mb-5 text-sm text-gray-400 dark:text-gray-500">
                        Esta acción no se puede deshacer.
                    </p>
                    <button type="button" onclick="confirmBranchDelete()"
                        class="inline-flex items-center rounded-lg bg-red-600 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-300 dark:focus:ring-red-800">
                        Sí, eliminar
                    </button>
                    <button type="button" onclick="closeBranchDeleteModal()"
                        class="ms-3 rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let branchCounter = 0;
        let branchToDelete = null;

        function closeBranchDeleteModal() {
            document.getElementById('delete-branch-modal').classList.add('hidden');
            branchToDelete = null;
        }

        function confirmBranchDelete() {
            if (branchToDelete) {
                const branchItem = branchToDelete.closest('.branch-item');
                const branchId = branchItem.dataset.id;
                
                if (branchId) {
                    // Si es una sucursal existente, agregar input hidden para eliminarla
                    const form = document.querySelector('form');
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'deleted_branches[]';
                    input.value = branchId;
                    form.appendChild(input);
                    
                    console.log('Sucursal marcada para eliminar:', branchId);
                }
                
                // Remover visualmente la sucursal
                branchItem.remove();
                closeBranchDeleteModal();
            }
        }

        document.getElementById('use-branches')?.addEventListener('change', function(e) {
            const branchesContainer = document.getElementById('branches-container');
            if (e.target.checked) {
                branchesContainer.classList.remove('hidden');
            } else {
                branchesContainer.classList.add('hidden');
            }
        });

        document.getElementById('add-branch-btn')?.addEventListener('click', function() {
            addBranchRow();
        });

        function addBranchRow(data = null) {
            const branchesList = document.getElementById('branches-list');
            const branchIndex = branchCounter++;
            
            const branchHtml = `
                <div class="branch-item border border-gray-300 dark:border-gray-600 rounded-lg p-4 relative" data-index="${branchIndex}">
                    <button type="button" class="absolute top-2 right-2 text-red-500 hover:text-red-700 remove-branch-btn">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Código de Sucursal <span class="text-red-500">*</span></label>
                            <input type="text" name="branches[${branchIndex}][branch_code]" 
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2.5" 
                                placeholder="Ej: SUC001" value="${data?.branch_code || ''}" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre de Sucursal <span class="text-red-500">*</span></label>
                            <input type="text" name="branches[${branchIndex}][nombre]" 
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2.5" 
                                placeholder="Nombre de la sucursal" value="${data?.nombre || ''}" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Departamento <span class="text-red-500">*</span></label>
                            <select name="branches[${branchIndex}][departamento]" 
                                class="branch-departamento w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2.5" 
                                data-branch-index="${branchIndex}" required>
                                <option value="">Seleccione un departamento</option>
                                @foreach($departamentos as $codigo => $nombre)
                                    <option value="{{ $codigo }}">{{ $nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Municipio <span class="text-red-500">*</span></label>
                            <select name="branches[${branchIndex}][municipio]" 
                                class="branch-municipio-${branchIndex} w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2.5" 
                                required>
                                <option value="">Primero seleccione un departamento</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dirección <span class="text-red-500">*</span></label>
                            <textarea name="branches[${branchIndex}][complemento]" 
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2.5" 
                                rows="2" placeholder="Dirección completa de la sucursal" required>${data?.complemento || ''}</textarea>
                        </div>
                    </div>
                </div>
            `;
            
            branchesList.insertAdjacentHTML('beforeend', branchHtml);
            attachBranchEventListeners(branchIndex);
        }

        function attachBranchEventListeners(branchIndex) {
            const departamentoSelect = document.querySelector(`[data-branch-index="${branchIndex}"]`);
            departamentoSelect?.addEventListener('change', async function(e) {
                const departamento = e.target.value;
                const municipioSelect = document.querySelector(`.branch-municipio-${branchIndex}`);
                
                if (departamento) {
                    municipioSelect.innerHTML = '<option value="">Cargando...</option>';
                    try {
                        const response = await fetch(`{{ route('business.get-municipios') }}?codigo=${departamento}`);
                        const municipios = await response.json();
                        
                        municipioSelect.innerHTML = '<option value="">Seleccione un municipio</option>';
                        Object.entries(municipios.municipios).forEach(([codigo, nombre]) => {
                            municipioSelect.innerHTML += `<option value="${codigo}">${nombre}</option>`;
                        });
                    } catch (error) {
                        console.error('Error cargando municipios:', error);
                        municipioSelect.innerHTML = '<option value="">Error al cargar municipios</option>';
                    }
                } else {
                    municipioSelect.innerHTML = '<option value="">Primero seleccione un departamento</option>';
                }
            });
        }

        // Añadir listeners para departamentos existentes
        document.querySelectorAll('[data-branch-index^="existing-"]').forEach(select => {
            select.addEventListener('change', async function(e) {
                const departamento = e.target.value;
                const branchIndex = e.target.dataset.branchIndex;
                const municipioSelect = document.querySelector(`.branch-municipio-${branchIndex}`);
                
                if (departamento) {
                    municipioSelect.innerHTML = '<option value="">Cargando...</option>';
                    try {
                        const response = await fetch(`{{ route('business.get-municipios') }}?departamento=${departamento}`);
                        const municipios = await response.json();
                        
                        const currentValue = municipioSelect.value;
                        municipioSelect.innerHTML = '<option value="">Seleccione un municipio</option>';
                        Object.entries(municipios).forEach(([codigo, nombre]) => {
                            const selected = codigo === currentValue ? 'selected' : '';
                            municipioSelect.innerHTML += `<option value="${codigo}" ${selected}>${nombre}</option>`;
                        });
                    } catch (error) {
                        console.error('Error cargando municipios:', error);
                        municipioSelect.innerHTML = '<option value="">Error al cargar municipios</option>';
                    }
                } else {
                    municipioSelect.innerHTML = '<option value="">Primero seleccione un departamento</option>';
                }
            });
        });

        // Listener para remover sucursales con confirmación
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-branch-btn')) {
                e.preventDefault();
                const button = e.target.closest('.remove-branch-btn');
                branchToDelete = button;
                
                const branchItem = button.closest('.branch-item');
                const branchName = branchItem.querySelector('input[name*="[nombre]"]')?.value || 'esta sucursal';
                
                // Mostrar nombre en el modal
                document.getElementById('branch-name-to-delete').textContent = branchName;
                
                // Mostrar modal
                document.getElementById('delete-branch-modal').classList.remove('hidden');
            }
        });

        // Cerrar modal al hacer clic fuera
        document.getElementById('delete-branch-modal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeBranchDeleteModal();
            }
        });
    </script>
    @endpush
@endsection
