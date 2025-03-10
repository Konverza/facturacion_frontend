@extends('layouts.auth-template')
@section('title', 'Generar comprobante de retención')
@section('content')
    <section class="my-4 pb-4">
        @include('layouts.partials.business.dte.header', [
            'title' => 'Generar comprobante de retención',
        ])
        <form action="{{ Route('business.dte.comprobante-retencion') }}" method="POST">
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
                                        class="show-modal w-full sm:w-auto" icon="user"
                                        data-target="#selected-customer" />
                                </div>
                                <div class="mt-4">
                                    <div class="flex flex-col gap-4 sm:flex-row">
                                        <div class="flex-1" id="select-tipos-documentos">
                                            <x-select label="Tipo de documento" name="tipo_documento" id="type_document"
                                                value="{{ old('tipo_documento', isset($dte['customer']) ? $dte['customer']['tipoDocumento'] : '') }}"
                                                selected="{{ old('tipo_documento', isset($dte['customer']) ? $dte['customer']['tipoDocumento'] : '') }}"
                                                :options="$tipos_documentos" />
                                        </div>
                                        <div class="flex-1">
                                            <x-input type="text" label="Número documento" id="numero_documento_customer"
                                                required name="numero_documento"
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
                                            id="nombre_comercial_customer"
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
                                </div>
                            </div>
                            <div class="hidden" id="styled-emisor" role="tabpanel" aria-labelledby="emisor-tab">
                                @include('layouts.partials.business.dte.sections.section-datos-emisor')
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Sección datos del emisor y receptor -->

                <!-- Sección documentos comprobante de retención -->
                <div class="mt-4 border-t border-gray-300 px-4 pt-4 dark:border-gray-800">
                    <h2 class="flex items-center gap-1 text-xl font-semibold text-primary-500 dark:text-primary-300">
                        <x-icon icon="info-circle" class="size-5" />
                        Documentos
                    </h2>
                    <div class="mt-4 flex flex-col items-center justify-start gap-4 sm:flex-row">
                        <div class="relative w-full sm:w-auto">
                            <x-button type="button" icon="arrow-down" typeButton="info" text="Agregar documentos"
                                class="show-options w-full sm:w-auto" data-target="#options-documents-1" size="normal"
                                data-align="right" />
                            <div class="options absolute right-0 top-0 z-10 mt-1 hidden w-calc-full-minus-8 rounded-lg border border-gray-300 bg-white p-1 dark:border-gray-800 dark:bg-gray-950 sm:w-max"
                                id="options-documents-1">
                                <ul class="flex flex-col text-sm">
                                    <li>
                                        <button type="button"
                                            class="show-modal flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900"
                                            data-target="#add-documento-fisico">
                                            <x-icon icon="file" class="h-4 w-4" />
                                            Físico
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button"
                                            class="btn-add-document-electric flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                            <x-icon icon="devices" class="h-4 w-4" />
                                            Electrónico
                                        </button>
                                    </li>
                                    {{--  <li>
                                        <button type="button"
                                            class="show-modal flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900"
                                            data-target="#taxes-iva">
                                            <x-icon icon="circle-off" class="h-4 w-4" />
                                            Operaciones con no inscritos IVA
                                        </button>
                                    </li> --}}
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <x-table :datatable="false">
                            <x-slot name="thead">
                                <x-tr>
                                    <x-th>Tipo documento</x-th>
                                    <x-th>Número documento</x-th>
                                    <x-th>Código retención</x-th>
                                    <x-th>Descripción</x-th>
                                    <x-th>Fecha</x-th>
                                    <x-th>Monto sujeto a retención</x-th>
                                    <x-th>IVA retenido</x-th>
                                    <x-th :last="true">Acciones</x-th>
                                </x-tr>
                                <x-slot name="tbody" id="table-documents-retention">
                                    @include('layouts.partials.ajax.business.table-documents-retention')
                                </x-slot>
                            </x-slot>
                        </x-table>
                        <div class="mt-4 flex items-center gap-2">
                            <h3 class="text-base font-semibold text-primary-500 dark:text-primary-300">
                                Valor en letras IVA retenido
                            </h3>
                            <span id="iva-retenido-letters"
                                class="text-sm font-medium uppercase text-gray-600 dark:text-gray-400">
                                {{ isset($dte['total_iva_retenido_texto']) ? $dte['total_iva_retenido_texto'] : '' }}
                            </span>
                        </div>
                    </div>
                </div>
                <!-- End Sección documentos comprobante de retención -->
            </div>
            @include('layouts.partials.business.dte.button-actions')
        </form>
    </section>

    @include('layouts.partials.business.dte.modals.modal-select-customer')

    <!-- Modal add documento fisico -->
    <div id="add-documento-fisico" tabindex="-1" aria-hidden="true"
        class="fixed left-0 right-0 top-0 z-[100] hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
        <div class="relative mb-4 max-h-full w-full max-w-2xl p-4">
            <!-- Modal content -->
            <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                <form action="{{ Route('business.dte.documents.store') }}" method="POST" class="flex flex-col">
                    @csrf
                    <!-- Modal header -->
                    <div
                        class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Documento físico
                        </h3>
                        <button type="button"
                            class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                            data-target="#add-documento-fisico">
                            <x-icon icon="x" class="h-5 w-5" />
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="flex flex-col gap-4 p-4">
                        <span class="flex items-center gap-1 text-base font-semibold text-gray-500 dark:text-gray-300">
                            <x-icon icon="info-circle" class="size-5" />
                            Datos del documento fisico
                        </span>
                        <x-select :options="[
                            '01' => 'Factura electrónica',
                            '03' => 'Comprobantre de crédito fiscal',
                            '14' => 'Factura de sujeto excluido',
                        ]" name="tipo_generacion" id="tipo_documento_fisico"
                            label="Tipo de documento" :search="false" required />
                        <div class="flex flex-col items-center gap-4 sm:flex-row">
                            <div class="sm:flex-1 w-full">
                                <x-input type="text" label="Número de documento" required id="numero_documento_fisico"
                                    placeholder="Ingresar el número del documento" name="numero_documento" />
                            </div>
                            <div class="sm:flex-1 w-full">
                                <x-input type="date" id="fecha_documento" label="Fecha de documento"
                                    name="fecha_documento" required />
                            </div>
                        </div>
                        <x-input type="textarea" label="Descripción" name="descripcion_retencion" required
                            id="descripción_document" placeholder="Ingresar la descripción del documento" />
                        <x-input type="number" name="monto_sujeto_retencion" label="Monto sujeto a retención" required
                            icon="currency-dollar" id="monto_sujeto_retencion" step="0.01" placeholder="0.00" />
                        <div class="flex flex-col items-center gap-4 sm:flex-row">
                            <div class="sm:flex-[2] w-full">
                                <x-select label="Código de tributo" :options="[
                                    '22' => 'Retención IVA',
                                    'C9' => 'Otras retenciones IVA (casos especiales)',
                                ]" id="codigo_tributo" value="22"
                                    selected="22" name="codigo_tributo" required />
                            </div>
                            <div class="w-full sm:flex-1">
                                <x-input type="number" name="iva_retenido" id="iva_retenido" label="Monto de IVA"
                                    placeholder="0.00" step="0.01" icon="currency-dollar" required />
                            </div>
                        </div>
                    </div>
                    <!-- Modal footer -->
                    <div
                        class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                        <x-button type="button" class="hide-modal" text="Cancelar" icon="x"
                            typeButton="secondary" data-target="#add-documento-fisico" />
                        <x-button type="button" class="submit-form" text="Agregar" icon="save"
                            typeButton="primary" />
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Modal add add documento fisico -->

    <!-- Modal add documento electronico -->
    <div id="add-documento-electronico" tabindex="-1" aria-hidden="true"
        class="fixed left-0 right-0 top-0 z-[100] hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
        <div class="relative max-h-full w-full max-w-[950px]">
            <!-- Modal content -->
            <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                <div class="flex flex-col">
                    <!-- Modal header -->
                    <div
                        class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Documento electrónico
                        </h3>
                        <button type="button"
                            class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                            data-target="#add-documento-electronico">
                            <x-icon icon="x" class="h-5 w-5" />
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="flex flex-col gap-4 p-4">
                        <span class="flex items-center gap-1 text-base font-semibold text-gray-500 dark:text-gray-300">
                            <x-icon icon="info-circle" class="size-5" />
                            Criterios de consulta
                        </span>
                        <div class="flex flex-col gap-4 sm:flex-row">
                            <div class="flex-1">
                                <x-input type="text" label="Número de documento" name="nit_receptor" readonly
                                    placeholder="Ingresar el número de documento" id="nit-receptor"
                                    value="{{ old('numero_documento', isset($dte['customer']) ? $dte['customer']['numDocumento'] : '') }}" />
                            </div>
                            <div class="flex-1">
                                <x-select :options="[
                                    'Factura electrónica' => 'Factura electrónica',
                                    'Comprobante de crédito fiscal' => 'Comprobante de crédito fiscal',
                                    'Factura de sujeto excluido' => 'Factura de sujeto excluido',
                                ]" name="tipo_documento" id="tipo_documento"
                                    label="Tipo de documento" :search="false" />
                            </div>
                        </div>
                        <div class="flex flex-col gap-4 sm:flex-row">
                            <div class="flex-1">
                                <x-input type="date" id="emitido-desde" name="emitido-desde"
                                    label="Fecha emitido desde" />
                            </div>
                            <div class="flex-1">
                                <x-input type="date" label="Fecha emitido hasta" name="emitido-hasta"
                                    id="emitido-hasta" />
                            </div>
                        </div>
                    </div>
                    <div class="px-4 pb-4">
                        <span
                            class="mb-2 flex items-center gap-1 text-base font-semibold text-gray-500 dark:text-gray-300">
                            <x-icon icon="info-circle" class="size-5" />
                            Resultados de la consulta
                        </span>
                        <div id="container-table-dtes">
                            <x-table id="table-dtes">
                                <x-slot name="thead">
                                    <x-tr>
                                        <x-th>Tipo</x-th>
                                        <x-th>NIT</x-th>
                                        <x-th>Fecha de emisión</x-th>
                                        <x-th>Código de generación</x-th>
                                        <x-th></x-th>
                                    </x-tr>
                                </x-slot>
                                <x-slot name="tbody">
                                    @foreach ($dtes as $dte)
                                        <x-tr>
                                            <x-td class="text-xs">
                                                @if ($dte['tipo_dte'] == '03')
                                                    Comprobante de crédito fiscal
                                                @elseif($dte['tipo_dte'] == '14')
                                                    Factura de sujeto excluido
                                                @else
                                                    Factura electrónica
                                                @endif
                                            </x-td>
                                            <x-td class="text-xs">
                                                @php
                                                    $documento = json_decode($dte['documento'], true);
                                                @endphp
                                                @if (isset($documento['receptor']['numDocumento']))
                                                    {{ $documento['receptor']['numDocumento'] }}
                                                @elseif(isset($documento['receptor']['nit']))
                                                    {{ $documento['receptor']['nit'] }}
                                                @elseif(isset($documento['sujetoExcluido']))
                                                    {{ $documento['sujetoExcluido']['numDocumento'] }}
                                                @endif
                                            </x-td>
                                            <x-td class="text-xs">
                                                {{ \Carbon\Carbon::parse($dte['fhProcesamiento'])->format('d/m/Y') }}
                                            </x-td>
                                            <x-td class="text-xs">
                                                {{ $dte['codGeneracion'] }}
                                            </x-td>
                                            <x-td :last="true">
                                                <x-button type="button" icon="arrow-next" size="small"
                                                    data-url="{{ route('business.dte.documents.selected') }}"
                                                    typeButton="secondary" text="Seleccionar"
                                                    data-cod="{{ $dte['codGeneracion'] }}"
                                                    class="btn-selected-document" />
                                            </x-td>
                                        </x-tr>
                                    @endforeach
                                </x-slot>
                            </x-table>
                        </div>
                        <div id="container-data-document" class="hidden">
                            <form action="{{ Route('business.dte.documents.store-electric') }}" method="POST">
                                @csrf
                                <div class="mt-4 flex flex-col gap-4">
                                    <input type="hidden" id="cod-generacion" name="cod_generacion">
                                    <x-input type="textarea" label="Descripción" name="descripcion_retencion"
                                        id="descripcion_retencion" placeholder="Ingresar la descripción del documento"
                                        required />
                                    <x-input type="number" name="monto_sujeto_retencion"
                                        label="Monto sujeto a retención" icon="currency-dollar" id="monto-documento"
                                        step="0.01" placeholder="0.00" required />
                                    <div class="flex flex-col items-center gap-4 sm:flex-row">
                                        <div class="flex-[2]">
                                            <x-select label="Código de tributo" :options="[
                                                '22' => 'Retención IVA',
                                                'C9' => 'Otras retenciones IVA (casos especiales)',
                                            ]" id="codigo-tributo-2"
                                                value="22" selected="22" required name="codigo_tributo"
                                                :search="false" />
                                        </div>
                                        <div class="flex-1">
                                            <x-input type="number" name="iva_retenido" id="iva-retenido-documento"
                                                label="Monto de IVA" placeholder="0.00" step="0.01"
                                                icon="currency-dollar" required />
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 flex items-center justify-center dark:border-gray-800">
                                    <x-button type="button" class="submit-form" text="Agregar" icon="save"
                                        typeButton="primary" />
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Modal footer -->
                    <div
                        class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                        <x-button type="button" class="hide-modal" text="Cancelar" icon="x"
                            typeButton="secondary" data-target="#add-documento-electronico" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal add add documento electronico -->

    @include('layouts.partials.business.dte.modals.modal-taxes-iva')
    @include('layouts.partials.business.dte.modals.modal-cancel-dte')
@endsection

@push('scripts')
    @vite('resources/js/dte.js')
@endpush
