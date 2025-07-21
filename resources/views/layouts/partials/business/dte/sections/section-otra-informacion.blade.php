@props([
    'documentos_relaciones' => true,
    'venta_cuenta_terceros' => true,
    'otros_documentos_asociados' => true,
    'medico' => true,
])

<!-- Sección otra información del DTE -->
<div class="mt-4 border-t border-gray-300 px-4 pt-4 dark:border-gray-800">
    <h2 class="flex items-center gap-1 text-xl font-semibold text-primary-500 dark:text-primary-300">
        <x-icon icon="info-circle" class="size-5" />
        Otra información del DTE
    </h2>
    <div>
        <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
            <ul class="-mb-px flex flex-nowrap overflow-x-auto text-center text-sm font-medium" id="default-styled-tab"
                data-tabs-toggle="#default-styled-tab-content"
                data-tabs-active-classes="text-primary-500 hover:text-primary-600 dark:text-primary-300 dark:hover:text-primary-400 border-primary-500 dark:border-primary-300"
                data-tabs-inactive-classes="dark:border-transparent text-gray-500 hover:text-gray-600 dark:text-gray-400 border-gray-100 hover:border-gray-300 dark:border-gray-700 dark:hover:text-gray-300"
                role="tablist">
                @if ($documentos_relaciones)
                    <li class="me-2" role="presentation">
                        <button
                            class="inline-block text-nowrap rounded-t-lg border-b-2 p-4 hover:border-gray-300 hover:text-gray-600 dark:hover:text-gray-300"
                            id="documentos-relacionados-styled-tab" data-tabs-target="#styled-documentos-relacionados"
                            type="button" role="tab" aria-controls="documentos-relacionados" aria-selected="false">
                            Documentos relacionados
                        </button>
                    </li>
                @endif

                @if ($venta_cuenta_terceros)
                    <li class="me-2" role="presentation">
                        <button
                            class="inline-block text-nowrap rounded-t-lg border-b-2 p-4 hover:border-gray-300 hover:text-gray-600 dark:hover:text-gray-300"
                            id="venta-cuenta-terceros-styled-tab" data-tabs-target="#styled-venta-cuenta-terceros"
                            type="button" role="tab" aria-controls="venta-cuenta-terceros" aria-selected="false">
                            Venta a cuenta de terceros
                        </button>
                    </li>
                @endif

                @if ($otros_documentos_asociados)
                    <li class="me-2" role="presentation">
                        <button
                            class="inline-block text-nowrap rounded-t-lg border-b-2 p-4 hover:border-gray-300 hover:text-gray-600 dark:hover:text-gray-300"
                            id="otros-documentos-asociados-styled-tab"
                            data-tabs-target="#styled-otros-documentos-asociados" type="button" role="tab"
                            aria-controls="otros-documentos-asociados" aria-selected="false">
                            Otros documentos asociados
                        </button>
                    </li>
                @endif
            </ul>
        </div>
        <div id="default-styled-tab-content">
            <div class="hidden" id="styled-documentos-relacionados" role="tabpanel"
                aria-labelledby="documentos-relacionados-tab">
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
                                        class="show-modal flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900"
                                        data-target="#add-documento-electronico">
                                        <x-icon icon="device-laptop" class="size-4" />
                                        Documento electrónico
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
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
            <div class="hidden" id="styled-venta-cuenta-terceros" role="tabpanel"
                aria-labelledby="venta-cuenta-terceros-tab">
                <div class="mt-4">
                    <div class="flex flex-col gap-4 sm:flex-row">
                        <div class="flex-1">
                            <x-input type="text" label="NIT" name="nit_terceros"
                                placeholder="Ingresar el NIT del contribuyente"
                                value="{{ old('nit_terceros', isset($dte['venta_tercero']) ? $dte['venta_tercero']['nit'] ?? '' : '') }}" />
                        </div>
                        <div class="flex-1">
                            <x-input type="text" label="Nombre" name="nombre_terceros"
                                placeholder="Ingresar el nombre del contribuyente"
                                value="{{ old('nombre_terceros', isset($dte['venta_tercero']) ? $dte['venta_tercero']['nombre'] ?? '' : '') }}" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="hidden" id="styled-otros-documentos-asociados" role="tabpanel"
                aria-labelledby="otros-documentos-asociados-tab">
                <div>
                    <x-button type="button" text="Agregar documentos" typeButton="info" icon="plus"
                        data-target="#add-otros-documentos-asociados" class="show-modal w-full sm:w-auto" />
                </div>
                <div class="mt-4">
                    <p class="mb-2 text-sm font-semibold text-gray-500 dark:text-gray-300">
                        Otros documentos asociados
                    </p>
                    @if($number === '15')
                        <div class="mb-4 border-l-4 border-red-500 bg-red-100 p-4 text-sm text-red-700 dark:bg-red-950/50 dark:text-red-300"
                            role="alert">
                            <div class="flex justify-start gap-2">
                                <x-icon icon="info-circle" class="h-5 w-5" />
                                Sección obligatoria, debe ingresar el
                                detalle de al menos un documento asociado 
                            </div>
                        </div>
                    @endif
                    <x-table :datatable="false">
                        <x-slot name="thead">
                            <x-tr>
                                <x-th>Código</x-th>
                                <x-th>Descripción</x-th>
                                <x-th>Detalle</x-th>
                                @if ($number === '11')
                                    <x-th>Placas</x-th>
                                    <x-th>Modo de transporte</x-th>
                                    <x-th>Número de identificación</x-th>
                                    <x-th>Nombre del conductor</x-th>
                                @endif
                                <x-th :last="true">Acciones</x-th>
                            </x-tr>
                        </x-slot>
                        <x-slot name="tbody" id="table-associated-documents">
                            @include('layouts.partials.ajax.business.table-associated-documents')
                        </x-slot>
                    </x-table>
                </div>
                @if ($number !== '11' && $medico)
                    <div class="mt-4">
                        <p class="mb-2 text-sm font-semibold text-gray-500 dark:text-gray-300">
                            Medicos relacionados
                        </p>
                        <x-table :datatable="false">
                            <x-slot name="thead">
                                <x-tr>
                                    <x-th>Nombre</x-th>
                                    <x-th>Tpo de servicio</x-th>
                                    <x-th>Número documento</x-th>
                                    <x-th>Identificación de documento</x-th>
                                    <x-th :last="true">Acciones</x-th>
                                </x-tr>
                            </x-slot>
                            <x-slot name="tbody" id="table-associated-doctors">
                                @include('layouts.partials.ajax.business.table-associated-doctors')
                            </x-slot>
                        </x-table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- End Sección otra información del DTE -->
