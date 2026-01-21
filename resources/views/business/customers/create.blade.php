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
                Nuevo cliente
            </h1>
            <a href="{{ Route('business.customers.index') }}"
                class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
                <x-icon icon="arrow-back" class="size-5" />
                Regresar
            </a>
        </div>
        <div class="mt-4 rounded-lg pb-4">
            <form action="{{ Route('business.customers.store') }}" method="POST">
                @csrf
                <div class="flex flex-col gap-4 sm:flex-row">
                    <div class="flex-1">
                        <x-select label="Tipo de documento" name="tipo_documento" id="type_document" :options="$tipos_documentos"
                            value="{{ old('tipo_documento') }}" selected="{{ old('tipo_documento') }}" />
                    </div>
                    <div class="flex-[2]">
                        <x-input type="text" label="Número de documento" name="numero_documento"
                            value="{{ old('numero_documento') }}" placeholder="Ingresar el número de documento"
                            id="numero_documento" />
                    </div>
                </div>
                <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                    <div class="flex-1">
                        <x-input type="text" label="Nombre / Razón social" name="nombre" value="{{ old('nombre') }}"
                            placeholder="Ingresa el nombre o razón social" id="nombre" />
                    </div>
                    <div class="flex-1">
                        <x-input type="text" label="NRC" name="nrc" id="nrc" value="{{ old('nrc') }}" />
                    </div>
                </div>
                <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                    <div class="flex-1">
                        <x-input type="text" label="Nombre comercial" name="nombre_comercial" id="nombre_comercial"
                            placeholder="Ingresar el nombre comercial" value="{{ old('nombre_comercial') }}" />
                    </div>
                    <div class="flex-1">
                        <x-select id="actividad_economica" :options="$actividades_economicas" label="Actividad económica"
                            name="actividad_economica" value="{{ old('actividad_economica') }}"
                            selected="{{ old('actividad_economica') }}" />
                    </div>
                </div>
                <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                    <div class="flex-1">
                        <x-select name="department" label="Departamento" id="departamento" name="departamento" required
                            :options="$departamentos" value="{{ old('departamento') }}" selected="{{ old('departamento') }}"
                            data-action="{{ Route('business.get-municipios') }}" />
                    </div>
                    <div class="flex-1" id="select-municipio">
                        <x-select name="municipio" label="Municipio" id="municipality" required :options="[
                            'Selecciona un municipio' => 'Seleccione un municipio',
                        ]"
                            value="{{ old('municipio') }}" />
                    </div>
                </div>
                <div class="mt-4">
                    <x-input type="textarea" label="Dirección" name="complemento" id="complemento"
                        placeholder="Ingresar la dirección" value="{{ old('complemento') }}" />
                </div>
                <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                    <div class="flex-1">
                        <x-input type="text" label="Correo electrónico" icon="email" name="correo" id="correo"
                            placeholder="example@examp.com" value="{{ old('email') }}" />
                    </div>
                    <div class="flex-1">
                        <x-input type="text" label="Teléfono" icon="phone" name="telefono" id="telefono"
                            placeholder="XXXX XXXX" value="{{ old('phone') }}" />
                    </div>
                </div>
                @if ($business->show_special_prices && !$business->price_variants_enabled)
                    <div class="mt-4">
                        <x-input type="checkbox" label="Aplicar precio especial a este cliente" name="special_price"
                            id="special_price" />
                    </div>
                @endif
                @if ($business->price_variants_enabled)
                    <div class="mt-4">
                        <label for="price_variant_id" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Variante de precio
                        </label>
                        <select name="price_variant_id" id="price_variant_id"
                            class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-600 focus:ring-primary-600 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500">
                            <option value="">Precio base</option>
                            @foreach ($priceVariants as $variant)
                                <option value="{{ $variant->id }}">{{ $variant->name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Si no se selecciona variante, se utilizará el precio base.
                        </p>
                    </div>
                @endif
                @if ($business->has_customer_branches)
                    <div class="mt-4">
                        <x-input type="checkbox" label="Este cliente tiene sucursales" name="use_branches"
                            id="use-branches" />
                    </div>
                @endif
                <div class="mt-4">
                    <x-input type="checkbox" label="Rellenar datos de exportación" name="export_data" id="export-data" />
                </div>
                <div class="hidden" id="export-data-container">
                    <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                        <div class="flex-1">
                            <x-select label="País" name="codigo_pais" id="codigo_pais" :options="$countries" />
                        </div>
                        <div class="flex-1">
                            <x-select label="Tipo de persona" name="tipo_persona" id="tipo_persona" :options="[
                                '1' => 'Persona natural',
                                '2' => 'Persona jurídica',
                            ]" />
                        </div>
                    </div>
                </div>
                
                {{-- Sección de sucursales --}}
                <div class="hidden mt-4" id="branches-container">
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Sucursales del Cliente</h3>
                            <x-button type="button" typeButton="secondary" text="Agregar Sucursal" 
                                class="text-sm" icon="add" id="add-branch-btn" />
                        </div>
                        <div id="branches-list" class="space-y-3">
                            {{-- Las sucursales se agregarán aquí --}}
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-center">
                    <x-button type="submit" typeButton="primary" text="Guardar cliente" class="w-full sm:w-auto"
                        icon="save" />
                </div>
            </form>
        </div>
    </section>

    @push('scripts')
    <script>
        let branchCounter = 0;

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

        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-branch-btn')) {
                e.target.closest('.branch-item').remove();
            }
        });
    </script>
    @endpush
@endsection
