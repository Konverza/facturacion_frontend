@extends('layouts.auth-template')
@section('title', 'Generar factura de sujeto excluido')
@section('content')
    <section class="my-4 pb-4">
        @include('layouts.partials.business.dte.header', ['title' => 'Generar factura de sujeto excluido'])
        <form action="{{ Route('business.dte.factura-sujeto-excluido') }}" method="POST">
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
                                        <div class="flex-1" id="select-tipos-documentos">
                                            <x-select label="Tipo de documento" name="tipo_documento" id="type_document"
                                                value="{{ old('tipo_documento', isset($dte['customer']) ? $dte['customer']['tipoDocumento'] : '') }}"
                                                selected="{{ old('tipo_documento', isset($dte['customer']) ? $dte['customer']['tipoDocumento'] : '') }}"
                                                :options="$tipos_documentos" />
                                        </div>
                                        <div class="flex-[2]">
                                            <x-input type="text" label="Número de documento"
                                                id="numero_documento_customer" required name="numero_documento"
                                                value="{{ old('numero_documento', isset($dte['customer']) ? $dte['customer']['numDocumento'] : '') }}"
                                                placeholder="Ingresar el número de documento" />
                                        </div>
                                    </div>

                                    <div class="mt-4 flex gap-4">
                                        <div class="flex-1">
                                            <x-input type="text" id="nombre_customer" name="nombre_customer"
                                                value="{{ old('nombre_customer', isset($dte['customer']) ? $dte['customer']['nombre'] : '') }}"
                                                label="Nombre, denominacióm o razón socvial del contribuyente"
                                                placeholder="Ingresa el nombre completo del receptor" />
                                        </div>
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
                                </div>
                            </div>
                            <div class="hidden" id="styled-emisor" role="tabpanel" aria-labelledby="emisor-tab">
                                @include('layouts.partials.business.dte.sections.section-datos-emisor')
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Sección datos del emisor y receptor -->

                <!-- Sección detalles de la factura -->
                <div class="mt-4 border-t border-gray-300 px-4 pt-4 dark:border-gray-800">
                    <h2 class="flex items-center gap-1 text-xl font-semibold text-primary-500 dark:text-primary-300">
                        <x-icon icon="info-circle" class="size-5" />
                        Datos de la factura
                    </h2>
                    <div class="mt-4 flex flex-col items-center justify-start gap-4 sm:flex-row">
                        <div class="relative w-full sm:w-auto">
                            <x-button type="button" icon="plus" typeButton="info" text="Agregar producto"
                                class="show-drawer w-full sm:w-auto" size="normal" data-target="#drawer-new-product"
                                aria-controls="drawer-new-product" />
                        </div>
                    </div>
                    <div class="mt-4" id="table-sujeto-excluido">
                        @include('layouts.partials.ajax.business.table-sujeto-excluido')
                    </div>
                    <div class="mt-4 flex flex-col justify-between gap-4 sm:flex-row">
                        <x-button type="button" text="Agregar descuento" typeButton="info" icon="plus"
                            data-target="#add-discount" class="show-modal w-full sm:w-auto" />
                        <x-button type="button" text="Eliminar descuento" typeButton="danger" icon="trash"
                            data-target="#delete-discount" class="show-modal delete-discounts w-full sm:w-auto"
                            data-action="{{ Route('business.dte.product.remove-discounts') }}" />
                    </div>
                </div>
                <!-- End Sección detalles de la factura -->

                @include('layouts.partials.business.dte.sections.section-observaciones')
                @include('layouts.partials.business.dte.sections.section-condicion-operacion')
                @include('layouts.partials.business.dte.sections.section-forma-pago')
            </div>
            @include('layouts.partials.business.dte.button-actions')
        </form>
    </section>

    @include('layouts.partials.business.dte.modals.modal-select-customer')
    @include('layouts.partials.business.dte.modals.modal-select-product')
    @include('layouts.partials.business.dte.drawer-new-product')
    @include('layouts.partials.business.dte.modals.modal-add-discount')
    @include('layouts.partials.business.dte.modals.modal-cancel-dte')

@endsection

@push('scripts')
    @vite('resources/js/dte.js')
@endpush
