@extends('layouts.auth-template')
@section('title', 'Generar nota de débito')
@section('content')
    <section class="my-4 pb-4">
        @include('layouts.partials.business.dte.header', ['title' => 'Generar nota de débito'])
        <form action="{{ Route('business.dte.nota-debito') }}" method="POST">
            @csrf
            @include('layouts.partials.business.dte.data-top-dte')
            <input type="hidden" name="id_dte" value="{{ $dte['id'] ?? '' }}">
            <div class="mt-4">
                <!-- Sección datos del emisor y receptor -->
                <div class="mt-4 border-t border-gray-300 px-4 pt-4 dark:border-gray-800">
                    <h2 class="flex items-center gap-1 text-xl font-semibold text-primary-500 dark:text-primary-300">
                        <x-icon icon="users" class="size-5" />
                        Datos del emisor y receptor
                    </h2>
                    @if (!$default_pos)
                        <div
                            class="my-4 rounded-lg border border-dashed border-yellow-500 bg-yellow-100 p-4 text-yellow-500 dark:bg-yellow-950/30">
                            <b>Nota: </b> No tiene un punto de venta predeterminado, por favor seleccione uno en la pestaña "Emisor" antes de enviar el DTE
                        </div>
                    @endif
                    <div>
                        <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
                            <ul class="-mb-px flex flex-wrap text-center text-sm font-medium" id="default-styled-tab"
                                data-tabs-toggle="#default-styled-tab-content"
                                data-tabs-active-classes="text-primary-500 hover:text-primary-600 dark:text-primary-300 dark:hover:text-primary-400 border-primary-500 dark:border-primary-300"
                                data-tabs-inactive-classes="dark:border-transparent text-gray-500 hover:text-gray-600 dark:text-gray-400 border-gray-100 hover:border-gray-300 dark:border-gray-700 dark:hover:text-gray-300"
                                role="tablist">
                                <li class="me-2" role="presentation">
                                    <button
                                        class="inline-block rounded-t-lg border-b-2 p-4 hover:border-gray-300 hover:text-gray-600 dark:hover:text-gray-300"
                                        id="receptor-styled-tab" data-tabs-target="#styled-receptor" type="button"
                                        role="tab" aria-controls="receptor" aria-selected="false">
                                        Receptor
                                    </button>
                                </li>
                                <li class="me-2" role="presentation">
                                    <button class="inline-block rounded-t-lg border-b-2 p-4" id="emisor-styled-tab"
                                        data-tabs-target="#styled-emisor" type="button" role="tab"
                                        aria-controls="emisor" aria-selected="false">
                                        Emisor
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div id="default-styled-tab-content">
                            <div class="hidden" id="styled-receptor" role="tabpanel" aria-labelledby="receptor-tab">
                                <div class="flex flex-col items-center justify-end gap-y-4 sm:flex-row">
                                    <x-button type="button" text="Seleccionar cliente existente" typeButton="success"
                                        data-target="#selected-customer" class="show-modal w-full sm:w-auto"
                                        icon="user" />
                                </div>
                                <div class="mt-4">
                                    <div class="flex flex-col gap-4 sm:flex-row">
                                        <div class="flex-1">
                                            <x-input type="text" label="NIT/DUI" id="numero_documento_customer" required
                                                name="numero_documento"
                                                value="{{ old('numero_documento', isset($dte['customer']) ? $dte['customer']['numDocumento'] : '') }}"
                                                placeholder="Número de documento" />
                                        </div>
                                        <div class="flex-1">
                                            <x-input type="text" label="Número de Registro de Contribuyente (NRC)"
                                                placeholder="Número de documento" id="nrc_customer"
                                                value="{{ old('nrc_customer', isset($dte['customer']) ? $dte['customer']['nrc'] : '') }}"
                                                name="nrc_customer" />
                                        </div>
                                    </div>
                                    <div class="mt-4 flex gap-4">
                                        <div class="flex-1">
                                            <x-input type="text" id="nombre_customer" name="nombre_customer"
                                                value="{{ old('nombre_customer', isset($dte['customer']) ? $dte['customer']['nombre'] : '') }}"
                                                label="Nombre del cliente"
                                                placeholder="Ingresa el nombre completo del cliente " />
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <x-input type="text" label="Nombre comercial" name="nombre_comercial"
                                            id="nombre_comercial_customer" value="{{ old('nombre_comercial') }}"
                                            placeholder="Ingresar el nombre comercial del receptor" />
                                    </div>
                                    <div class="mt-4" id="select-actividad-economica">
                                        <x-select id="actividad_economica_customer" :options="$actividades_economicas"
                                            label="Actividad económica" name="actividad_economica"
                                            value="{{ old('actividad_economica', isset($dte['customer']) ? $dte['customer']['codActividad'] : '') }}"
                                            selected="{{ old('actividad_economica', isset($dte['customer']) ? $dte['customer']['codActividad'] : '') }}" />
                                    </div>
                                    <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                                        <div class="flex-1" id="select-departamentos">
                                            <x-select name="department" label="Departamento" id="departamento"
                                                name="departamento" required :options="$departamentos"
                                                value="{{ old('departamento', isset($dte['customer']) ? $dte['customer']['departamento'] : '') }}"
                                                selected="{{ old('departamento', isset($dte['customer']) ? $dte['customer']['departamento'] : '') }}"
                                                data-action="{{ Route('business.get-municipios') }}" />
                                        </div>
                                        <div class="flex-1" id="select-municipio">
                                            <x-select name="municipio" label="Municipio" id="municipality" required
                                                :options="$municipios ?? [
                                                    'Seleccione un departamento' => 'Seleccione un departamento',
                                                ]"
                                                selected="{{ old('municipio', isset($dte['customer']) ? $dte['customer']['municipio'] : '') }}"
                                                value="{{ old('municipio', isset($dte['customer']) ? $dte['customer']['municipio'] : '') }}" />
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <x-input type="textarea" label="Complemento" name="complemento"
                                            placeholder="Ingresar el complemento de la dirección"
                                            value="{{ old('complemento', isset($dte['customer']) ? $dte['customer']['complemento'] : '') }}"
                                            id="complemento_customer" />
                                    </div>
                                    <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                                        <div class="flex-1">
                                            <x-input type="text" label="Correo electrónico" icon="email"
                                                value="{{ old('correo', isset($dte['customer']) ? $dte['customer']['correo'] : '') }}"
                                                required name="correo" placeholder="example@examp.com"
                                                id="correo_customer" />
                                        </div>
                                        <div class="flex-1">
                                            <x-input type="text" label="Teléfono" icon="phone" name="telefono"
                                                value="{{ old('telefono', isset($dte['customer']) ? $dte['customer']['telefono'] : '') }}"
                                                required placeholder="XXXX XXXX" id="telefono_customer" />
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <x-input type="text" label="Número de Orden de Compra (Opcional)" 
                                            name="orden_compra" id="orden_compra_customer"
                                            value="{{ old('orden_compra', isset($dte['orden_compra']) ? $dte['orden_compra'] : '') }}"
                                            placeholder="Ingrese el número de orden de compra" />
                                    </div>
                                    @include('layouts.partials.business.dte.sections.section-customer-branch')
                                </div>
                            </div>
                            <div class="hidden" id="styled-emisor" role="tabpanel" aria-labelledby="emisor-tab">
                                @include('layouts.partials.business.dte.sections.section-datos-emisor')
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Sección datos del emisor y receptor -->

                <!-- Sección documentos relacionados -->
                <div class="mt-4 border-t border-gray-300 px-4 pt-4 dark:border-gray-800">
                    <h2 class="flex items-center gap-1 text-xl font-semibold text-primary-500 dark:text-primary-300">
                        <x-icon icon="info-circle" class="size-5" />
                        Documentos relacionados
                    </h2>
                    <div class="mt-4">
                        <div class="relative w-full">
                            <x-button type="button" icon="arrow-down" typeButton="info" text="Agregar DTE"
                                class="show-options w-full sm:w-auto" data-target="#options-add-dte" size="normal"
                                data-align="right" />
                            <div class="options absolute right-0 top-0 z-10 mt-1 hidden w-calc-full-minus-8 rounded-lg border border-gray-300 bg-white p-1 dark:border-gray-800 dark:bg-gray-950 sm:w-max"
                                id="options-add-dte">
                                <ul class="flex flex-col text-sm">
                                    <li>
                                        <button type="button"
                                            class="show-modal flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900"
                                            data-target="#add-documento-fisico">
                                            <x-icon icon="file" class="size-4" />
                                            Documento físico
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button"
                                            class="btn-add-document-electric flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                            <x-icon icon="device-laptop" class="size-4" />
                                            Documento electrónico
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="mb-4 border-l-4 border-red-500 bg-red-100 p-4 text-sm text-red-700 dark:bg-red-950/50 dark:text-red-300"
                            role="alert">
                            <div class="flex justify-start gap-2">
                                <x-icon icon="info-circle" class="h-5 w-5" />
                                Sección obligatoria, debe ingresar el
                                detalle de al menos un documento relacionado
                            </div>
                        </div>
                        <x-table :datatable="false">
                            <x-slot name="thead">
                                <x-tr>
                                    <x-th>Tipo de documento</x-th>
                                    <x-th>Tipo de generación</x-th>
                                    <x-th>Código de generación / correlativo</x-th>
                                    <x-th>Fecha de emisión</x-th>
                                    <x-th :last="true">Acciones</x-th>
                                </x-tr>
                                <x-slot name="tbody" id="table-related-documents">
                                    @if (isset($dte['documentos_relacionados']) && count($dte['documentos_relacionados']) > 0)
                                        @foreach ($dte['documentos_relacionados'] as $documento)
                                            <x-tr :last="$loop->last">
                                                <x-td>{{ $documento['tipo_documento'] }}</x-td>
                                                <x-td>{{ $documento['tipo_generacion'] }}</x-td>
                                                <x-td>{{ $documento['numero_documento'] }}</x-td>
                                                <x-td>{{ $documento['fecha_documento'] }}</x-td>
                                                <x-td :last="true">
                                                    <x-button type="button" icon="trash"
                                                        data-action="{{ Route('business.dte.related-documents.delete', $documento['id']) }}"
                                                        size="small" typeButton="danger" class="btn-delete"
                                                        text="Eliminar" />
                                                </x-td>
                                            </x-tr>
                                        @endforeach
                                    @else
                                        <x-tr :last="true">
                                            <x-td colspan="5" class="text-center" :last="true">
                                                No hay documentos relacionados
                                            </x-td>
                                        </x-tr>
                                    @endif
                                </x-slot>
                            </x-slot>
                        </x-table>
                    </div>
                </div>
                <!-- End sección documentos relacionados  -->

                <!-- Sección venta a cuenta de terceros -->
                <div class="mt-4 border-t border-gray-300 px-4 pt-4 dark:border-gray-800">
                    <h2 class="flex items-center gap-1 text-xl font-semibold text-primary-500 dark:text-primary-300">
                        <x-icon icon="info-circle" class="size-5" />
                        Venta por cuenta de terceros
                    </h2>
                    <div class="my-4 border-l-4 border-yellow-500 bg-yellow-100 p-4 text-sm text-yellow-700 dark:bg-yellow-950/50 dark:text-yellow-300"
                        role="alert">
                        <div class="flex justify-start gap-2">
                            <x-icon icon="info-circle" class="h-5 w-5" />
                            Sección no obligatoria, debe completarse si se actúa en calidad de comisionista
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex flex-col gap-4 sm:flex-row">
                            <div class="flex-1">
                                <x-input type="text" label="NIT" name=""
                                    placeholder="Ingresar el NIT del contribuyente" />
                            </div>
                            <div class="flex-1">
                                <x-input type="text" label="Nombre" name=""
                                    placeholder="Ingresar el nombre del contribuyente" />
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End sección venta a cuenta de terceros  -->

                <!-- Sección detalles de la factura -->
                <div class="mt-4 border-t border-gray-300 px-4 pt-4 dark:border-gray-800">
                    <h2 class="flex items-center gap-1 text-xl font-semibold text-primary-500 dark:text-primary-300">
                        <x-icon icon="info-circle" class="size-5" />
                        Datos de la nota de débito
                    </h2>
                    <div class="mt-4 flex flex-col items-center justify-start gap-4 sm:flex-row">
                        <div class="relative w-full sm:w-auto">
                            <x-button type="button" icon="arrow-down" typeButton="info" text="Agregar detalle"
                                class="show-options w-full sm:w-auto" data-target="#options-customers-2" size="normal"
                                data-align="right" />
                            <div class="options absolute right-0 top-0 z-10 mt-1 hidden w-calc-full-minus-8 rounded-lg border border-gray-300 bg-white p-1 dark:border-gray-800 dark:bg-gray-950 sm:w-max"
                                id="options-customers-2">
                                <ul class="flex flex-col text-sm">
                                    <li>
                                        <button type="button"
                                            class="new-item flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900"
                                            data-action="{{ Route('business.dte.related-documents.get') }}"
                                            data-type="new-product">
                                            <x-icon icon="cube-plus" class="h-4 w-4" />
                                            Producto o servicio
                                        </button>
                                    </li>
                                    {{--  <li>
                                        <button type="button" data-type="other-contribution"
                                            data-action="{{ Route('business.dte.related-documents.get') }}"
                                            class="new-item flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                            <x-icon icon="currency-dollar" class="h-4 w-4" />
                                            Otras contribuciones
                                        </button>
                                    </li> --}}
                                </ul>
                            </div>
                        </div>
                        <x-button type="button" text="Seleccionar producto existente" icon="arrow-double-next"
                            typeButton="success" data-type="selected-product" class="new-item w-full sm:w-auto"
                            data-action="{{ Route('business.dte.related-documents.get') }}" />
                    </div>
                    <div class="mt-4" id="table-products-dte">
                        @include('layouts.partials.ajax.business.table-products-dte')
                    </div>
                </div>
                <!-- End Sección detalles de la factura -->

                @include('layouts.partials.business.dte.sections.section-observaciones')
                @include('layouts.partials.business.dte.sections.section-condicion-operacion')
                @include('layouts.partials.business.dte.sections.section-forma-pago')
                @include('layouts.partials.business.dte.sections.section-recepcion-entrega-documento', ["monto" => "$11,428.58"])
            </div>
            @include('layouts.partials.business.dte.button-actions')
        </form>
    </section>
    @include('layouts.partials.business.dte.modals.modal-select-customer')
    @include('layouts.partials.business.dte.modals.modal-select-product')
    @include('layouts.partials.business.dte.drawer-new-product')
    @include('layouts.partials.business.dte.modals.modal-taxes-iva')
    @include('layouts.partials.business.dte.modals.modal-add-discount')
    @include('layouts.partials.business.dte.modals.modal-cancel-dte')
    @include('layouts.partials.business.dte.modals.modal-documento-fisico')
    @include('layouts.partials.business.dte.modals.modal-documento-electronico')
@endsection

@push('scripts')
    @vite('resources/js/dte.js')
    
    @if(isset($dte['customer']) && isset($dte['customer']['id']))
    <script>
        // Pre-cargar sucursales cuando hay un cliente seleccionado (edición de borrador/plantilla)
        document.addEventListener('DOMContentLoaded', function() {
            const customerId = {{ $dte['customer']['id'] ?? 'null' }};
            if (customerId) {
                axios.get(`/business/customers/${customerId}`)
                    .then((response) => {
                        const data = response.data;
                        
                        if (data.has_branches && data.branches && data.branches.length > 0) {
                            const branchSelect = $("#customer_branch_select");
                            branchSelect.empty();
                            branchSelect.append('<option value="">Seleccione una sucursal</option>');
                            
                            data.branches.forEach(branch => {
                                const selected = @json($dte['customer_branch']['id'] ?? null) == branch.id ? 'selected' : '';
                                branchSelect.append(
                                    `<option value="${branch.id}" ${selected}
                                        data-codigo="${branch.branch_code}" 
                                        data-nombre="${branch.nombre}"
                                        data-departamento="${branch.departamento}"
                                        data-municipio="${branch.municipio}"
                                        data-complemento="${branch.complemento || ''}">
                                        ${branch.branch_code} - ${branch.nombre}
                                    </option>`
                                );
                            });
                            
                            $("#customer-branches-section").removeClass("hidden");
                        }
                    })
                    .catch((error) => {
                        console.error("Error al cargar sucursales:", error);
                    });
            }
        });
    </script>
    @endif
@endpush
