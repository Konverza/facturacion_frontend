@extends('layouts.auth-template')
@section('title', 'Generar ' . $document_type)
@section('content')
    <section class="my-4 pb-4">
        @include('layouts.partials.business.dte.header', ['title' => 'Generar factura de exportación'])
        <form action="{{ Route('business.dte.factura-exportacion') }}" method="POST">
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
                                                label="Nombre del receptor"
                                                placeholder="Ingresa el nombre completo del receptor" />
                                        </div>
                                    </div>
                                    <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                                        <div class="flex-1" id="select-tipo-persona">
                                            <x-select name="tipo_persona" id="tipo_de_persona" label="Tipo de persona"
                                                value="{{ old('tipo_persona', isset($dte['customer']) ? $dte['customer']['tipoPersona'] ?? '' : '') }}"
                                                selected="{{ old('tipo_persona', isset($dte['customer']) ? $dte['customer']['tipoPersona'] ?? '' : '') }}"
                                                :options="['1' => 'Natural', '2' => 'Jurídica']" :search="false" />
                                        </div>
                                        <div class="flex-1" id="select-pais">
                                            <x-select label="País" name="codigo_pais" id="codigo_pais" :options="$countries"
                                                value="{{ old('codigo_pais', isset($dte['customer']) ? $dte['customer']['codPais'] ?? '' : '') }}"
                                                selected="{{ old('codigo_pais', isset($dte['customer']) ? $dte['customer']['codPais'] ?? '' : '') }}" />
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

                <!-- Sección detalles de la factura de exportación -->
                <div class="mt-4 border-t border-gray-300 px-4 pt-4 dark:border-gray-800">
                    <h2 class="flex items-center gap-1 text-xl font-semibold text-primary-500 dark:text-primary-300">
                        <x-icon icon="info-circle" class="size-5" />
                        Detalles de la factura de exportación
                    </h2>
                    <div class="mt-4 flex flex-col gap-4">
                        <x-select name="tipo_item_exportar" id="tipo_item_exportar" label="Tipo de ítem a exportar"
                            :options="['1' => 'Bienes', '2' => 'Servicios', '3' => 'Bienes y servicios']" :search="false"
                            value="{{ old('tipo_item_exportar', isset($dte['emisor']) ? $dte['emisor']['tipoItemExpor'] : '') }}"
                            selected="{{ old('tipo_item_exportar', isset($dte['emisor']) ? $dte['emisor']['tipoItemExpor'] : '') }}" />
                        <x-select name="recinto_fiscal" id="recinto_fiscal" label="Recinto fiscal" :options="$recintoFiscal"
                            value="{{ old('recinto_fiscal', isset($dte['emisor']) ? $dte['emisor']['recintoFiscal'] : '') }}"
                            selected="{{ old('recinto_fiscal', isset($dte['emisor']) ? $dte['emisor']['recintoFiscal'] : '') }}" />
                        <x-select name="regimen_exportacion" id="regimen_exportacion" label="Régimen exportación"
                            :options="$regimenExportacion"
                            value="{{ old('regimen_exportacion', isset($dte['emisor']) ? $dte['emisor']['regimen'] : '') }}"
                            selected="{{ old('regimen_exportacion', isset($dte['emisor']) ? $dte['emisor']['regimen'] : '') }}" />
                    </div>
                </div>
            </div>
            <!-- End Sección detalles de la factura de exportación -->

            <!-- Sección detalles de la factura -->
            <div class="mt-4 border-t border-gray-300 px-4 pt-4 dark:border-gray-800">
                <h2 class="flex items-center gap-1 text-xl font-semibold text-primary-500 dark:text-primary-300">
                    <x-icon icon="info-circle" class="size-5" />
                    Datos de la factura de exportación
                </h2>
                <div class="mt-4 flex flex-col items-center justify-start gap-4 sm:flex-row">
                    <div class="relative w-full sm:w-auto">
                        <x-button type="button" icon="plus" typeButton="info" text="Agregar producto"
                            class="show-drawer w-full sm:w-auto" size="normal" data-target="#drawer-new-product"
                            aria-controls="drawer-new-product" />
                    </div>
                    <x-button type="button" text="Seleccionar producto existente" icon="arrow-double-next"
                        typeButton="success" data-target="#selected-product" class="show-modal w-full sm:w-auto" />
                </div>
                <div class="mt-4" id="table-exportacion">
                    @include('layouts.partials.ajax.business.table-exportacion')
                </div>
            </div>
            <!-- End Sección detalles de la factura -->
            @include('layouts.partials.business.dte.sections.section-otra-informacion', [
                'documentos_relaciones' => false,
            ])
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

    <!-- Modal add otros-documentos-asociados -->
    <div id="add-otros-documentos-asociados" tabindex="-1" aria-hidden="true"
        class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
        <div class="relative max-h-full w-full max-w-xl p-4">
            <!-- Modal content -->
            <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                <form action="{{ Route('business.dte.associated-documents.store') }}" method="POST"
                    class="flex flex-col">
                    <!-- Modal header -->
                    @csrf
                    <div
                        class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Información del documento asociado
                        </h3>
                        <button type="button"
                            class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                            data-target="#add-otros-documentos-asociados">
                            <x-icon icon="x" class="h-5 w-5" />
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4">
                        <div class="flex flex-col gap-4">
                            <span class="flex items-center gap-1 text-base font-semibold text-gray-500 dark:text-gray-300">
                                <x-icon icon="info-circle" class="size-5" />
                                Datos generales del documento
                            </span>
                            <x-select :options="[
                                '1' => 'Emisor',
                                '2' => 'Receptor',
                                '4' => 'Transporte',
                            ]" name="documento_asociado" id="documento_asociado"
                                label="Documento asociado" :search="false" required />
                        </div>
                        <div class="mt-4 flex flex-col gap-4">
                            <x-input type="text" label="Identifación del documento" required
                                placeholder="Identificación del nombre del documento asociado"
                                name="identificacion_documento" id="identificacion_documento" />
                            <x-input type="textarea" label="Descripción del documento"
                                placeholder="Descripción de datos importantes del documento asociado"
                                name="descripcion_documento" id="descripcion_documento" required />
                        </div>
                        <div class="mt-4 hidden" id="container-data-transporte">
                            <div class="flex flex-col gap-4">
                                <div class="flex flex-col items-center gap-4 sm:flex-row">
                                    <div class="w-full sm:flex-1">
                                        <x-input type="text" label="Placas"
                                            placeholder="Número de identificación del medio de tranporte" name="placas"
                                            id="placas" required />
                                    </div>
                                    <div class="w-full sm:flex-1">
                                        <x-select :options="$modo_transporte" name="modo_transporte" id="modo_transporte"
                                            label="Modo transporte" required :search="false" />
                                    </div>
                                </div>
                                <div class="flex flex-col items-center gap-4 sm:flex-row">
                                    <div class="w-full sm:flex-1">
                                        <x-input type="text" label="Número de indentificación"
                                            placeholder="Número de documento de identificación del conductor"
                                            name="numero_identificacion" id="numero_identificacion" required />
                                    </div>
                                    <div class="w-full sm:flex-1">
                                        <x-input type="text" label="Nombre conductor"
                                            placeholder="Nomber completo del conductor" name="nombre_conductor"
                                            id="nombre_conductor" required />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal footer -->
                    <div
                        class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                        <x-button type="button" text="Cancelar" icon="x" typeButton="secondary"
                            data-target="#add-otros-documentos-asociados" class="hide-modal" />
                        <x-button type="button" class="submit-form" text="Agregar" icon="save"
                            typeButton="primary" />
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Modal add otros-documentos-asociados -->

@endsection

@push('scripts')
    @vite('resources/js/dte.js')
@endpush
