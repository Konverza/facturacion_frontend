@extends('layouts.auth-template')
@section('title', 'Generar ' . $document_type)
@section('content')
    <section class="my-4 pb-4">
        <div class="flex w-full items-center justify-between px-4">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                {{ $document_type }}
            </h1>
            <a href="{{ Route('business.customers.index') }}"
                class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
                <x-icon icon="arrow-back" class="size-5" />
                Regresar
            </a>
        </div>
        <form action="">
            <div class="mt-4 flex flex-col items-start justify-between gap-4 px-4 sm:flex-row sm:items-center">
                <div class="flex flex-col items-start gap-4 sm:flex-row sm:items-center">
                    <span class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
                        <x-icon icon="calendar" class="size-4" />
                        Fecha DTE:
                        {{ $currentDate }}
                    </span>
                    <div class="flex items-center gap-1 text-gray-600 dark:text-gray-400">
                        <x-icon icon="clock" class="size-4" />
                        <input type="time" id="time-in-real-time"
                            class="m-0 border-none bg-transparent p-0 text-sm focus:border-none focus:outline-none"
                            readonly>
                    </div>
                </div>
                <x-button type="button" typeButton="danger" text="Cancelar" icon="x" class="w-full sm:w-auto"
                    data-modal-target="cancel-dte" data-modal-toggle="cancel-dte" />
            </div>
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
                                <div class="flex flex-col items-center justify-between gap-y-4 sm:flex-row">
                                    <div>
                                        <x-input type="checkbox" label="Omitir datos del emisor"
                                            name="omitir_datos_emisor" />
                                    </div>
                                    <x-button type="button" text="Seleccionar cliente existente" typeButton="success"
                                        class="btn-selected-customer w-full sm:w-auto" icon="user" />
                                </div>
                                <div class="mt-4">
                                    <div class="flex flex-col gap-4 sm:flex-row">
                                        @if ($number === '03' || $number === '05' || $number === '06')
                                            <div class="flex-1">
                                                <x-input type="text" label="NIT / DUI" id="numero_documento_customer"
                                                    required name="numero_documento"
                                                    value="{{ old('numero_documento', isset($dte['customer']) ? $dte['customer']->numDocumento : '') }}"
                                                    placeholder="Número de documento" />
                                            </div>
                                            <div class="flex-1">
                                                <x-input type="text" label="Número de Registro de Contribuyente (NRC)"
                                                    placeholder="Número de documento" id="nrc_customer"
                                                    value="{{ old('nrc_customer', isset($dte['customer']) ? $dte['customer']->nrc : '') }}"
                                                    name="nrc_customer" />
                                            </div>
                                        @else
                                            <div class="flex-1" id="select-tipos-documentos">
                                                <x-select label="Tipo de documento" name="tipo_documento" id="type_document"
                                                    value="{{ old('tipo_documento', isset($dte['customer']) ? $dte['customer']->tipoDocumento : '') }}"
                                                    selected="{{ old('tipo_documento', isset($dte['customer']) ? $dte['customer']->tipoDocumento : '') }}"
                                                    :options="$tipos_documentos" />
                                            </div>
                                            <div class="flex-[2]">
                                                <x-input type="text" label="Número de documento"
                                                    id="numero_documento_customer" required name="numero_documento"
                                                    value="{{ old('numero_documento', isset($dte['customer']) ? $dte['customer']->numDocumento : '') }}"
                                                    placeholder="Ingresar el número de documento" />
                                            </div>
                                        @endif
                                    </div>

                                    @if($number !== "03" && $number !== "05" && $number !== "06" && $number !== "07" && $number !== "11" && $number !== "14")
                                        <div class="mt-4 flex gap-4">
                                            <div class="flex-1">
                                                <x-input type="text" id="nombre_customer" name="nombre_customer"
                                                    value="{{ old('nombre_customer', isset($dte['customer']) ? $dte['customer']->nombre : '') }}"
                                                    label="Nombre, denominación o razón social del contribuyente"
                                                    name="" placeholder="Ingresa el nombre o razón social" />
                                            </div>
                                        </div>
                                    @endif

                                    @if($number === "03" || $number === "05" || $number === "06" || $number === "07" || $number === "11" || $number === "14")
                                        <div class="mt-4 flex gap-4">
                                            <div class="flex-1">
                                                <x-input type="text" id="nombre_customer" name="nombre_customer"
                                                    value="{{ old('nombre_customer', isset($dte['customer']) ? $dte['customer']->nombre : '') }}"
                                                    label="Nombre del {{ $number === '11' || $number === '14' ? 'receptor' : 'cliente' }}"
                                                    name="" placeholder="Ingresa el nombre completo del {{ $number === '11' || $number === '14' ? 'receptor' : 'cliente' }} " />
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if ($number === '03' || $number === '05' || $number === '06' || $number === '07')
                                        <div class="mt-4">
                                            <x-input type="text" label="Nombre comercial" name="nombre_comercial"
                                                id="nombre_comercial_customer"
                                                placeholder="Ingresar el nombre comercial del receptor" />
                                        </div>
                                    @endif

                                    @if ($number === '11' || $number === '03' || $number === '05' || $number === '06' || $number === '07' || $number === '14')
                                        <div class="mt-4" id="select-actividad-economica">
                                            <x-select id="actividad_economica_customer" :options="$actividades_economicas"
                                                label="Actividad económica" name="actividad_economica"
                                                value="{{ old('actividad_economica') }}" />
                                        </div>
                                    @endif

                                    @if ($number === '11')
                                        <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                                            <div class="flex-1">
                                                <x-select name="tipo_de_persona" id="tipo_de_persona"
                                                    label="Tipo de persona" :options="['Natural', 'Jurídica']" />
                                            </div>
                                            <div class="flex-1">
                                                <x-select label="País" name="codigo_pais" id="codigo_pais"
                                                    :options="$countries" />
                                            </div>
                                        </div>
                                    @endif

                                    <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                                        <div class="flex-1" id="select-departamentos">
                                            <x-select name="department" label="Departamento" id="departamento"
                                                name="departamento" required :options="$departamentos"
                                                value="{{ old('departamento', isset($dte['customer']) ? $dte['customer']->departamento : '') }}"
                                                selected="{{ old('departamento', isset($dte['customer']) ? $dte['customer']->departamento : '') }}"
                                                data-action="{{ Route('business.get-municipios') }}" />
                                        </div>
                                        <div class="flex-1" id="select-municipio">
                                            <x-select name="municipio" label="Municipio" id="municipality" required
                                                :options="$municipios ?? [
                                                    'Seleccione un departamento' => 'Seleccione un departamento',
                                                ]"
                                                selected="{{ old('municipio', isset($dte['customer']) ? $dte['customer']->municipio : '') }}"
                                                value="{{ old('municipio', isset($dte['customer']) ? $dte['customer']->municipio : '') }}" />
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <x-input type="textarea" label="Complemento" name="complemento"
                                            placeholder="Ingresar el complemento de la dirección"
                                            value="{{ old('complemento', isset($dte['customer']) ? $dte['customer']->complemento : '') }}"
                                            id="complemento_customer" />
                                    </div>
                                    <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                                        <div class="flex-1">
                                            <x-input type="text" label="Correo electrónico" icon="email"
                                                value="{{ old('correo', isset($dte['customer']) ? $dte['customer']->correo : '') }}"
                                                name="correo" placeholder="example@examp.com" id="correo_customer" />
                                        </div>
                                        <div class="flex-1">
                                            <x-input type="text" label="Teléfono" icon="phone" name="telefono"
                                                value="{{ old('telefono', isset($dte['customer']) ? $dte['customer']->telefono : '') }}"
                                                placeholder="XXXX XXXX" id="telefono_customer" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="hidden" id="styled-emisor" role="tabpanel" aria-labelledby="emisor-tab">
                                <div class="flex flex-col gap-4 sm:flex-row">
                                    <div class="flex-1">
                                        <x-select id="actividad_economica_customer" :options="$actividades_economicas"
                                            label="Actividad económica" name="actividad_economica_emisor"
                                            value="{{ old('actividad_economica_emisor', $datos_empresa['codActividad']) }}"
                                            readonly
                                            selected="{{ old('actividad_economica_emisor', $datos_empresa['codActividad']) }}" />
                                    </div>
                                    <div class="flex-1">
                                        <x-select name="tipo_establecimiento" id="tipo_establecimiento"
                                            value="{{ old('tipo_establecimiento', $datos_empresa['tipoEstablecimiento']) }}"
                                            selected="{{ old('tipo_establecimiento', $datos_empresa['tipoEstablecimiento']) }}"
                                            readonly label="Tipo de establecimiento_emisor" :options="$tipos_establecimientos" />
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <x-input type="textarea" label="Establecimiento / dirección"
                                        name="complemento_emisor"
                                        value="{{ old('complemento_emisor', $datos_empresa['complemento']) }}" readonly
                                        placeholder="Ingresar la dirección" />
                                </div>
                                <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                                    <div class="flex-1">
                                        <x-input type="text" label="Correo electrónico" icon="email"
                                            name="correo_emisor" readonly placeholder="example@examp.com"
                                            value="{{ old('correo_emisor', $datos_empresa['correo']) }}" />
                                    </div>
                                    <div class="flex-1">
                                        <x-input type="text" label="Teléfono" icon="phone" name="telefono_emisor"
                                            placeholder="XXXX XXXX" readonly
                                            value="{{ old('telefono_emisor', $datos_empresa['telefono']) }}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Sección datos del emisor y receptor -->

                <!-- Sección detalles de la factura de exportación -->
                @if ($number === '11')
                    <div class="mt-6 border-t border-gray-300 px-4 pt-4 dark:border-gray-800">
                        <h2 class="flex items-center gap-1 text-xl font-semibold text-primary-500 dark:text-primary-300">
                            <x-icon icon="info-circle" class="size-5" />
                            Detalles de la factura de exportación
                        </h2>
                        <div class="mt-4 flex flex-col gap-4">
                            <div>
                                <x-select name="tipo_item_exportar" id="tipo_item_exportar"
                                    label="Tipo de ítem a exportar" :options="['Bienes', 'Servicios', 'Bienes y servicios']" />
                            </div>
                            <div>
                                <x-select name="recinto_fiscal" id="recinto_fiscal" label="Recinto fiscal"
                                    :options="$recintoFiscal" />
                            </div>
                            <div>
                                <x-select name="regimen_exportacion" id="regimen_exportacion" label="Régimen exportación"
                                    :options="$regimenExportacion" />
                            </div>
                        </div>
                    </div>
                @endif
                <!-- End Sección detalles de la factura de exportación -->

                <!-- Sección documentos comprobante de retención -->
                @if ($number === '07')
                    <div class="mt-4 border-t border-gray-300 px-4 pt-4 dark:border-gray-800">
                        <h2 class="flex items-center gap-1 text-xl font-semibold text-primary-500 dark:text-primary-300">
                            <x-icon icon="info-circle" class="size-5" />
                            Documentos
                        </h2>
                        <div class="mt-4 flex flex-col items-center justify-start gap-4 sm:flex-row">
                            <div class="relative w-full sm:w-auto">
                                <x-button type="button" icon="arrow-down" typeButton="info" text="Agregar documentos"
                                    class="show-options w-full sm:w-auto" data-target="#options-documents-1"
                                    size="normal" data-align="right" />
                                <div class="options absolute right-0 top-0 z-10 mt-1 hidden w-max rounded-lg border border-gray-300 bg-white p-2 dark:border-gray-800 dark:bg-gray-950"
                                    id="options-documents-1">
                                    <ul class="flex flex-col text-sm">
                                        <li>
                                            <button type="button"
                                                class="flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900"
                                                data-modal-target="add-documento-fisico"
                                                data-modal-toggle="add-documento-fisico">
                                                <x-icon icon="file" class="h-4 w-4" />
                                                Físico
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button"
                                                class="flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                <x-icon icon="devices" class="h-4 w-4" />
                                                Electrónico
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button"
                                                class="flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                <x-icon icon="circle-off" class="h-4 w-4" />
                                                Operaciones con no inscritos IVA
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
                                        <x-th>Tipo generación</x-th>
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
                                        @if (isset($dte['documentos_retencion']) && count($dte['documentos_retencion']) > 0)
                                            @foreach ($dte['documentos_retencion'] as $documento)
                                                <x-tr>
                                                    <x-td>
                                                        {{ $documento['tipo_generacion'] }}
                                                    </x-td>
                                                    <x-td>
                                                        {{ $documento['tipo_documento'] }}
                                                    </x-td>
                                                    <x-td>
                                                        {{ $documento['numero_documento'] }}
                                                    </x-td>
                                                    <x-td>
                                                        {{ $documento['codigo_retencion'] }}
                                                    </x-td>
                                                    <x-td>
                                                        {{ $documento['descripcion_retencion'] }}
                                                    </x-td>
                                                    <x-td>
                                                        {{ $documento['fecha_documento'] }}
                                                    </x-td>
                                                    <x-td>
                                                        ${{ number_format($documento['monto_sujeto_retencion'], 2) }}
                                                    </x-td>
                                                    <x-td>
                                                        ${{ number_format($documento['iva_retenido'], 2) }}
                                                    </x-td>
                                                    <x-td>
                                                        <x-button type="button" icon="trash" size="small"
                                                            data-action="{{ Route('business.dte.documents.delete', $documento['id']) }}"
                                                            typeButton="danger" onlyIcon class="btn-delete" />
                                                    </x-td>
                                                </x-tr>
                                            @endforeach
                                        @else
                                            <x-tr>
                                                <x-td colspan="9" class="text-center">No hay documentos</x-td>
                                            </x-tr>
                                        @endif
                                        <x-tr>
                                            <x-td colspan="9" :last="true">
                                                <div class="flex items-center justify-end gap-4 text-end">
                                                    Monto sujeto a retención
                                                    <span>
                                                        ${{ number_format($dte['monto_sujeto_retencion_total'] ?? 0, 2) }}
                                                    </span>
                                                </div>
                                            </x-td>
                                        </x-tr>
                                        <x-tr :last="true">
                                            <x-td colspan="9" :last="true">
                                                <div class="flex items-center justify-end gap-4 text-end">
                                                    Total IVA retenido
                                                    <span>
                                                        ${{ number_format($dte['total_iva_retenido'] ?? 0, 2) }}
                                                    </span>
                                                </div>
                                            </x-td>
                                        </x-tr>
                                    </x-slot>
                                </x-slot>
                            </x-table>
                            <div class="mt-4 flex items-center gap-2">
                                <h3 class="text-base font-semibold text-primary-500 dark:text-primary-300">
                                    Valor en letras IVA retenido
                                </h3>
                                <span id="iva-retenido-letters" class=" font-medium text-gray-600 dark:text-gray-400 text-sm uppercase">
                                    {{ isset($dte['total_iva_retenido_texto']) ? $dte['total_iva_retenido_texto'] : '' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endif
                <!-- End Sección documentos comprobante de retención -->

                <!-- Sección detalles de la factura -->
                @if (
                    $number === '03' ||
                        $number === '01' ||
                        $number === '05' ||
                        $number === '06' ||
                        $number === '11' ||
                        $number === '14')
                    <div class="mt-4 border-t border-gray-300 px-4 pt-4 dark:border-gray-800">
                        <h2 class="flex items-center gap-1 text-xl font-semibold text-primary-500 dark:text-primary-300">
                            <x-icon icon="info-circle" class="size-5" />
                            @if ($number === '11' || $number === '06' || $number === '05')
                                Cuerpo del documento
                            @else
                                Detalle de factura
                            @endif
                        </h2>
                        <div class="mt-4 flex flex-col items-center justify-start gap-4 sm:flex-row">
                            <div class="relative w-full sm:w-auto">
                                <x-button type="button" icon="arrow-down" typeButton="info" text="Agregar detalle"
                                    class="show-options w-full sm:w-auto" data-target="#options-customers-2"
                                    size="normal" data-align="right" />
                                <div class="options absolute right-0 top-0 z-10 mt-1 hidden w-max rounded-lg border border-gray-300 bg-white p-2 dark:border-gray-800 dark:bg-gray-950"
                                    id="options-customers-2">
                                    <ul class="flex flex-col text-sm">
                                        <li>
                                            <button type="button"
                                                class="flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900"
                                                data-drawer-target="drawer-new-product"
                                                data-drawer-show="drawer-new-product" aria-controls="drawer-new-product">
                                                <x-icon icon="pencil" class="h-4 w-4" />
                                                Producto o servicio
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button"
                                                class="flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                <x-icon icon="eye" class="h-4 w-4" />
                                                Monto no afecto
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button"
                                                class="flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                <x-icon icon="eye" class="h-4 w-4" />
                                                Impuestos / Tasas con afectación de IVA
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <x-button type="button" text="Seleccionar producto existente" icon="arrow-double-next"
                                typeButton="success" data-modal-target="selected-product"
                                data-modal-toggle="selected-product" class="w-full sm:w-auto" />
                        </div>
                        <div class="mt-4">
                            <x-table :datatable="false">
                                <x-slot name="thead">
                                    <x-tr>
                                        <x-th>Unidad de medida</x-th>
                                        <x-th>Descripción</x-th>
                                        <x-th>Cantidad</x-th>
                                        <x-th>Precio</x-th>
                                        <x-th>Descuento por item</x-th>
                                        <x-th>Venta gravada</x-th>
                                        <x-th>Venta exenta</x-th>
                                        <x-th>Venta no sujeta</x-th>
                                        <x-th :last="true"></x-th>
                                    </x-tr>
                                    <x-slot name="tbody" id="table-products-dte">
                                        @if (isset($dte['products']) && count($dte['products']) > 0)
                                            @foreach ($dte['products'] as $product)
                                                <x-tr>
                                                    <x-td>{{ $product['unidad_medida'] }}</x-td>
                                                    <x-td>{{ $product['descripcion'] }}</x-td>
                                                    <x-td>{{ $product['cantidad'] }}</x-td>
                                                    <x-td>${{ number_format($product['precio'], 2) }}</x-td>
                                                    <x-td>${{ number_format($product['descuento'], 2) }}</x-td>
                                                    <x-td>${{ number_format($product['ventas_gravadas'], 2) }}</x-td>
                                                    <x-td>${{ number_format($product['ventas_exentas'], 2) }}</x-td>
                                                    <x-td>${{ number_format($product['ventas_no_sujetas'], 2) }}</x-td>
                                                    <x-td :last="true">
                                                        <x-button type="button" icon="trash" size="small"
                                                            data-action="{{ Route('business.dte.product.delete', $product['id']) }}"
                                                            typeButton="danger" text="Eliminar"
                                                            class="btn-delete-product" />
                                                    </x-td>
                                                </x-tr>
                                            @endforeach
                                        @else
                                            <x-tr>
                                                <x-td colspan="9" class="text-center">No hay productos</x-td>
                                            </x-tr>
                                        @endif
                                        <x-tr>
                                            <x-td colspan="9" :last="true">
                                                <div class="flex items-center justify-end gap-4 text-end">
                                                    Subtotal
                                                    <span>
                                                        ${{ number_format($dte['subtotal'] ?? 0, 2) }}
                                                    </span>
                                                </div>
                                            </x-td>
                                        </x-tr>

                                        @if (isset($dte['turismo_por_alojamiento']) && $dte['turismo_por_alojamiento'] > 0)
                                            <x-tr>
                                                <x-td colspan="9" :last="true">
                                                    <div class="flex items-center justify-end gap-4 text-end">
                                                        Turismo por alojamiento
                                                        <span>
                                                            ${{ number_format($dte['turismo_por_alojamiento'], 2) }}
                                                        </span>
                                                    </div>
                                                </x-td>
                                            </x-tr>
                                        @endif

                                        @if (isset($dte['turismo_salida_pais_via_aerea']) && $dte['turismo_salida_pais_via_aerea'] > 0)
                                            <x-tr>
                                                <x-td colspan="9" :last="true">
                                                    <div class="flex items-center justify-end gap-4 text-end">
                                                        Turismo salida del país por vía aérea
                                                        <span>
                                                            ${{ number_format($dte['turismo_salida_pais_via_aerea'], 2) }}
                                                        </span>
                                                    </div>
                                                </x-td>
                                            </x-tr>
                                        @endif

                                        @if (isset($dte['fovial']) && $dte['fovial'] > 0)
                                            <x-tr>
                                                <x-td colspan="9" :last="true">
                                                    <div class="flex items-center justify-end gap-4 text-end">
                                                        FOVIAL ($0.20 por galón de combustible)
                                                        <span>
                                                            ${{ number_format($dte['fovial'], 2) }}
                                                        </span>
                                                    </div>
                                                </x-td>
                                            </x-tr>
                                        @endif

                                        @if (isset($dte['contrans']) && $dte['contrans'] > 0)
                                            <x-tr>
                                                <x-td colspan="9" :last="true">
                                                    <div class="flex items-center justify-end gap-4 text-end">
                                                        COTRANS ($0.10 por galón de combustible)
                                                        <span>
                                                            ${{ number_format($dte['contrans'], 2) }}
                                                        </span>
                                                    </div>
                                                </x-td>
                                            </x-tr>
                                        @endif

                                        @if (isset($dte['bebidas_alcoholicas']) && $dte['bebidas_alcoholicas'] > 0)
                                            <x-tr>
                                                <x-td colspan="9" :last="true">
                                                    <div class="flex items-center justify-end gap-4 text-end">
                                                        Impuesto ad-valorem por diferencial de precio de Bebidas Alcohólicas
                                                        (8%)
                                                        <span>
                                                            ${{ number_format($dte['bebidas_alcoholicas'], 2) }}
                                                        </span>
                                                    </div>
                                                </x-td>
                                            </x-tr>
                                        @endif

                                        @if (isset($dte['tabaco_cigarillos']) && $dte['tabaco_cigarillos'] > 0)
                                            <x-tr>
                                                <x-td colspan="9" :last="true">
                                                    <div class="flex items-center justify-end gap-4 text-end">
                                                        Impuesto ad-valorem por diferencial de precio al tabaco cigarrillos
                                                        (39%)
                                                        <span>
                                                            ${{ number_format($dte['tabaco_cigarillos'], 2) }}
                                                        </span>
                                                    </div>
                                                </x-td>
                                            </x-tr>
                                        @endif

                                        @if (isset($dte['tabaco_cigarros']) && $dte['tabaco_cigarros'] > 0)
                                            <x-tr>
                                                <x-td colspan="9" :last="true">
                                                    <div class="flex items-center justify-end gap-4 text-end">
                                                        Impuesto ad-valorem por diferencial de precio al tabaco cigarros
                                                        (100%)
                                                        <span>
                                                            ${{ number_format($dte['tabaco_cigarros'], 2) }}
                                                        </span>
                                                    </div>
                                                </x-td>
                                            </x-tr>
                                        @endif

                                        <x-tr>
                                            <x-td colspan="9" :last="true">
                                                <div class="flex items-center justify-end gap-4 text-end">
                                                    Monto total de la operación
                                                    <span>
                                                        ${{ number_format($dte['total'] ?? 0, 2) }}
                                                    </span>
                                                </div>
                                            </x-td>
                                        </x-tr>
                                        <x-tr>
                                            <x-td colspan="9" :last="true">
                                                <div class="flex items-center justify-end">
                                                    <div class="me-10">
                                                        <x-input type="checkbox" label="¿Retener IVA?" name="retener_iva"
                                                            data-action="{{ Route('business.dte.product.withhold') }}"
                                                            id="retener_iva" :checked="isset($dte['retener_iva']) && $dte['retener_iva']  === 'active'" class="retener"
                                                            data-type="iva" />
                                                    </div>
                                                    <div class="flex items-center justify-end gap-4 text-end">
                                                        Retención IVA (1%)
                                                        <span>
                                                            @if (isset($dte['retener_iva']) && $dte['retener_iva'] === 'active')
                                                                ${{ number_format($dte['iva'], 2) }}
                                                            @else
                                                                $0.00
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                            </x-td>
                                        </x-tr>
                                        <x-tr>
                                            <x-td colspan="9" :last="true">
                                                <div class="flex items-center justify-end">
                                                    <div class="me-10">
                                                        <x-input type="checkbox" label="¿Retener renta?"
                                                            name="retener_renta"
                                                            data-action="{{ Route('business.dte.product.withhold') }}"
                                                            id="retener_renta" class="retener" data-type="renta"
                                                            :checked="isset($dte['retener_renta']) && $dte['retener_renta'] === 'active'" />
                                                    </div>
                                                    <div class="flex items-center justify-end gap-4 text-end">
                                                        Retención renta
                                                        <span>
                                                            @if (isset($dte['retener_renta']) && $dte['retener_renta'] === 'active')
                                                                ${{ number_format($dte['isr'], 2) }}
                                                            @else
                                                                $0.00
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                            </x-td>
                                        </x-tr>
                                        <x-tr>
                                            <x-td colspan="9" :last="true">
                                                <div class="flex items-center justify-end gap-4 text-end">
                                                    Descuento a operación
                                                    <span>
                                                        $0.00
                                                    </span>
                                                </div>
                                            </x-td>
                                        </x-tr>
                                        <x-tr :last="true">
                                            <x-td colspan="9" :last="true">
                                                <div class="flex items-center justify-end gap-4 text-end">
                                                    Total pagar
                                                    <span>
                                                        ${{ number_format($dte['total_pagar'] ?? 0, 2) }}
                                                    </span>
                                                </div>
                                            </x-td>
                                        </x-tr>

                                    </x-slot>
                                </x-slot>
                            </x-table>
                            <div class="mt-4">
                                <x-button type="button" text="Agregar descuento" typeButton="info" icon="plus"
                                    data-modal-target="add-discount" class="w-full sm:w-auto"
                                    data-modal-toggle="add-discount" />
                            </div>
                        </div>
                    </div>
                @endif
                <!-- End Sección detalles de la factura -->

                <!-- Sección otra información del DTE -->
                @if ($number !== '14' && $number !== '07')
                    <div class="mt-4 border-t border-gray-300 px-4 pt-4 dark:border-gray-800">
                        <h2 class="flex items-center gap-1 text-xl font-semibold text-primary-500 dark:text-primary-300">
                            <x-icon icon="info-circle" class="size-5" />
                            Otra información del DTE
                        </h2>
                        <div>
                            <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
                                <ul class="-mb-px flex flex-nowrap overflow-x-auto text-center text-sm font-medium"
                                    id="default-styled-tab" data-tabs-toggle="#default-styled-tab-content"
                                    data-tabs-active-classes="text-primary-500 hover:text-primary-600 dark:text-primary-300 dark:hover:text-primary-400 border-primary-500 dark:border-primary-300"
                                    data-tabs-inactive-classes="dark:border-transparent text-gray-500 hover:text-gray-600 dark:text-gray-400 border-gray-100 hover:border-gray-300 dark:border-gray-700 dark:hover:text-gray-300"
                                    role="tablist">
                                    @if ($number !== '11')
                                        <li class="me-2" role="presentation">
                                            <button
                                                class="inline-block text-nowrap rounded-t-lg border-b-2 p-4 hover:border-gray-300 hover:text-gray-600 dark:hover:text-gray-300"
                                                id="documentos-relacionados-styled-tab"
                                                data-tabs-target="#styled-documentos-relacionados" type="button"
                                                role="tab" aria-controls="documentos-relacionados"
                                                aria-selected="false">
                                                Documentos relacionados
                                            </button>
                                        </li>
                                    @endif
                                    <li class="me-2" role="presentation">
                                        <button
                                            class="inline-block text-nowrap rounded-t-lg border-b-2 p-4 hover:border-gray-300 hover:text-gray-600 dark:hover:text-gray-300"
                                            id="venta-cuenta-terceros-styled-tab"
                                            data-tabs-target="#styled-venta-cuenta-terceros" type="button"
                                            role="tab" aria-controls="venta-cuenta-terceros" aria-selected="false">
                                            Venta a cuenta de terceros
                                        </button>
                                    </li>
                                    @if ($number !== '05' && $number !== '06' && $number !== '05')
                                        <li class="me-2" role="presentation">
                                            <button
                                                class="inline-block text-nowrap rounded-t-lg border-b-2 p-4 hover:border-gray-300 hover:text-gray-600 dark:hover:text-gray-300"
                                                id="otros-documentos-asociados-styled-tab"
                                                data-tabs-target="#styled-otros-documentos-asociados" type="button"
                                                role="tab" aria-controls="otros-documentos-asociados"
                                                aria-selected="false">
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
                                            <x-button type="button" icon="arrow-down" typeButton="info"
                                                text="Agregar DTE" class="show-options w-full sm:w-auto"
                                                data-target="#options-add-dte" size="normal" data-align="right" />
                                            <div class="options absolute right-0 top-0 z-10 mt-1 hidden w-max rounded-lg border border-gray-300 bg-white p-2 dark:border-gray-800 dark:bg-gray-950"
                                                id="options-add-dte">
                                                <ul class="flex flex-col text-sm">
                                                    <li>
                                                        <button type="button"
                                                            class="flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900"
                                                            data-modal-target="add-documento-fisico"
                                                            data-modal-toggle="add-documento-fisico">
                                                            Documento físico
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button type="button"
                                                            class="flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900"
                                                            data-modal-target="add-documento-electronico"
                                                            data-modal-toggle="add-documento-electronico">
                                                            Documento electrónico
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        @if ($number === '05' || $number === '06')
                                            <div class="mb-4 border-l-4 border-red-500 bg-red-100 p-4 text-sm text-red-700 dark:bg-red-950/50 dark:text-red-300"
                                                role="alert">
                                                <div class="flex justify-start gap-2">
                                                    <x-icon icon="info-circle" class="h-5 w-5" />
                                                    Documentos relacionados (sección obligatoria, debe ingresar el
                                                    detalle de al menos un documento relacionado)
                                                </div>
                                            </div>
                                        @endif
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
                                                                        size="small" typeButton="danger"
                                                                        class="btn-delete" text="Eliminar" />
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

                                    @if ($number === '05' || $number === '06')
                                        <div class="mb-4 border-l-4 border-yellow-500 bg-yellow-100 p-4 text-sm text-yellow-700 dark:bg-yellow-950/50 dark:text-yellow-300"
                                            role="alert">
                                            <div class="flex justify-start gap-2">
                                                <x-icon icon="info-circle" class="h-5 w-5" />
                                                Venta por cuenta de terceros (sección no obligatoria, debe completarse si se
                                                actúa en calidad de comisionista)
                                            </div>
                                        </div>
                                    @endif

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
                                <div class="hidden" id="styled-otros-documentos-asociados" role="tabpanel"
                                    aria-labelledby="otros-documentos-asociados-tab">
                                    <div>
                                        <x-button type="button" text="Agregar documentos" typeButton="info"
                                            icon="plus" data-modal-target="add-otros-documentos-asociados"
                                            data-modal-toggle="add-otros-documentos-asociados" class="w-full sm:w-auto" />
                                    </div>
                                    <div class="mt-4">
                                        <p class="mb-2 text-sm font-semibold text-gray-500 dark:text-gray-300">
                                            Otros documentos asociados
                                        </p>
                                        <x-table :datatable="false">
                                            <x-slot name="thead">
                                                <x-tr>
                                                    <x-th>Código del documento</x-th>
                                                    <x-th>Descripción del documento</x-th>
                                                    <x-th>Detalle del documento</x-th>
                                                    <x-th :last="true">Acciones</x-th>
                                                </x-tr>
                                            </x-slot>
                                            <x-slot name="tbody" id="table-associated-documents">
                                                @if (isset($dte['documentos_asociados']) && count($dte['documentos_asociados']) > 0)
                                                    @foreach ($dte['documentos_asociados'] as $documento)
                                                        <x-tr :last="$loop->last">
                                                            <x-td>
                                                                {{ $documento['documento_asociado'] }}
                                                            </x-td>
                                                            <x-td>
                                                                {{ $documento['identificacion_documento'] }}
                                                            </x-td>
                                                            <x-td>
                                                                {{ $documento['descripcion_documento'] }}
                                                            </x-td>
                                                            <x-td :last="true">
                                                                <x-button type="button" icon="trash" size="small"
                                                                    typeButton="danger" class="btn-delete"
                                                                    text="Eliminar"
                                                                    data-action="{{ Route('business.dte.associated-documents.delete', $documento['id']) }}" />
                                                            </x-td>
                                                        </x-tr>
                                                    @endforeach
                                                @else
                                                    <x-tr :last="true">
                                                        <x-td colspan="4" class="text-center" :last="true">
                                                            No hay documentos asociados
                                                        </x-td>
                                                    </x-tr>
                                                @endif
                                            </x-slot>
                                        </x-table>
                                    </div>
                                    @if (isset($dte['medicos_relacionados']) && count($dte['medicos_relacionados']) > 0)
                                        <div class="mt-4">
                                            <p class="mb-2 text-sm font-semibold text-gray-500 dark:text-gray-300">
                                                Medicos relacionados
                                            </p>
                                            <x-table :datatable="false">
                                                <x-slot name="thead">
                                                    <x-tr>
                                                        <x-th>Nombre</x-th>
                                                        <x-th>Tpo de servicio</x-th>
                                                        <x-th>NIT</x-th>
                                                        <x-th>Identificación de documento</x-th>
                                                        <x-th :last="true">Acciones</x-th>
                                                    </x-tr>
                                                </x-slot>
                                                <x-slot name="tbody" id="table-associated-doctors">
                                                    @if (isset($dte['medicos_relacionados']) && count($dte['medicos_relacionados']) > 0)
                                                        @foreach ($dte['medicos_relacionados'] as $medico)
                                                            <x-tr :last="$loop->last">
                                                                <x-td>
                                                                    {{ $medico['nombre'] }}
                                                                </x-td>
                                                                <x-td>
                                                                    {{ $medico['tipo_servicio'] }}
                                                                </x-td>
                                                                <x-td>
                                                                    {{ $medico['nit'] }}
                                                                </x-td>
                                                                <x-td>
                                                                    {{ $medico['tipo_documento'] }}
                                                                </x-td>
                                                                <x-td :last="true">
                                                                    <x-button type="button" icon="trash"
                                                                        size="small" typeButton="danger"
                                                                        class="btn-delete" text="Eliminar"
                                                                        data-action="{{ Route('business.dte.associated-documents.delete-doctor', $medico['id']) }}" />
                                                                </x-td>
                                                            </x-tr>
                                                        @endforeach
                                                    @else
                                                        <x-tr :last="true">
                                                            <x-td colspan="4" class="text-center" :last="true">
                                                                No hay documentos asociados
                                                            </x-td>
                                                        </x-tr>
                                                    @endif
                                                </x-slot>
                                            </x-table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <!-- End Sección otra información del DTE -->

                <!-- Sección observaciones  -->
                @if ($number !== '07')
                    <div class="mt-4 border-t border-gray-300 px-4 pt-4 dark:border-gray-800">
                        <h2 class="flex items-center gap-1 text-xl font-semibold text-primary-500 dark:text-primary-300">
                            <x-icon icon="info-circle" class="size-5" />
                            Observaciones
                        </h2>
                        <div class="mt-2">
                            <x-input type="textarea" label="Observaciones" name=""
                                placeholder="Observaciones al documento" />
                        </div>
                    </div>
                @endif
                <!-- End Sección observaciones  -->

                <!-- Sección condición de la operación -->
                @if ($number !== '07')
                    <div class="mt-4 border-t border-gray-300 px-4 pt-4 dark:border-gray-800">
                        <h2 class="flex items-center gap-1 text-xl font-semibold text-primary-500 dark:text-primary-300">
                            <x-icon icon="info-circle" class="size-5" />
                            Condición de la operación
                        </h2>
                        <div class="mt-2">
                            <x-select id="condicion_operacion" name="condicion_operacion"
                                label="Condición de la operación" name="" :options="[
                                    'Contado' => 'Contado',
                                    'Crédito' => 'Crédito',
                                    'Otro' => 'Otro',
                                ]" />
                        </div>
                    </div>
                @endif
                <!-- End Sección condición de la operación -->

                <!-- Sección forma de pago -->
                @if ($number !== '05' && $number !== '06' && $number !== '07')
                    <div class="mt-4 border-t border-gray-300 px-4 pt-4 dark:border-gray-800">
                        <h2 class="flex items-center gap-1 text-xl font-semibold text-primary-500 dark:text-primary-300">
                            <x-icon icon="info-circle" class="size-5" />
                            Forma de pago
                        </h2>
                        <div class="mt-2 flex flex-col gap-4 md:flex-row">
                            <div class="flex-[2]" id="input-forma-pago">
                                <x-select id="forma_de_pago" name="forma_pago" label="Forma de pago"
                                    :options="[
                                        'Billetes y monedas' => 'Billetes y monedas',
                                        'Tarjeta de crédito' => 'Tarjeta de crédito',
                                        'Cheque' => 'Cheque',
                                        'Transferencia / depósito bancario' => 'Transferencia / depósito bancario',
                                        'Vales o cupones' => 'Vales o cupones',
                                        'Dinero electrónico' => 'Dinero electrónico',
                                        'Monedero electrónico' => 'Monedero electrónico',
                                        'Certificado o tarjeta de regalo' => 'Certificado o tarjeta de regalo',
                                        'Bitcoin' => 'Bitcoin',
                                        'Otras criptomonedas' => 'Otras criptomonedas',
                                        'Cuentas por pagar del receptor' => 'Cuentas por pagar del receptor',
                                        'Giro bancario' => 'Giro bancario',
                                        'Otros (especificar)' => 'Otros (especificar)',
                                    ]" />
                            </div>
                            <div class="flex-1">
                                <x-input type="number" label="Monto" name="monto" id="monto"
                                    icon="currency-dollar" placeholder="0.00" value="{{ $dte['total_pagar'] ?? 0 }}" />
                            </div>
                            <div class="flex-[2]">
                                <x-input type="text" label="Número de documento" id="numero_documento"
                                    name="numero_documento" placeholder="Ingresa el número del documento" />
                            </div>
                            <div class="hidden flex-1" id="input-plazo">
                                <x-select label="Plazo" id="plazo" name="plazo" :options="['Días', 'Meses', 'Años']" />
                            </div>
                            <div class="hidden flex-1" id="input-periodo">
                                <x-input type="number" label="Período" name="periodo" id="periodo"
                                    placeholder="0" />
                            </div>
                            <x-button type="button" onlyIcon icon="plus" typeButton="success"
                                id="btn-add-forma-pago" class="md:mt-6"
                                data-action="{{ Route('business.dte.payment-method.store') }}" />
                        </div>
                        <div class="mt-4">
                            <x-table :datatable="false">
                                <x-slot name="thead">
                                    <x-tr>
                                        <x-th>Forma de pago</x-th>
                                        <x-th>Monto</x-th>
                                        <x-th>Número de documento</x-th>
                                        <x-th>Plazo</x-th>
                                        <x-th>Período</x-th>
                                        <x-th :last="true">Acciones</x-th>
                                    </x-tr>
                                </x-slot>
                                <x-slot name="tbody" id="table-formas-pago">
                                    @if (isset($dte['metodos_pago']) && count($dte['metodos_pago']) > 0)
                                        @foreach ($dte['metodos_pago'] as $metodo_pago)
                                            <x-tr :last="true">
                                                <x-td>{{ $metodo_pago['forma_pago'] }}</x-td>
                                                <x-td>${{ $metodo_pago['monto'] }}</x-td>
                                                <x-td>{{ $metodo_pago['numero_documento'] }}</x-td>
                                                <x-td>{{ $metodo_pago['plazo'] }}</x-td>
                                                <x-td>{{ $metodo_pago['periodo'] }}</x-td>
                                                <x-td :last="true">
                                                    <x-button type="button" icon="trash" size="small"
                                                        typeButton="danger" text="Eliminar" class="btn-delete-method"
                                                        data-action="{{ Route('business.dte.payment-method.delete', $metodo_pago['id']) }}" />
                                                </x-td>
                                            </x-tr>
                                        @endforeach
                                    @else
                                        <x-tr :last="true">
                                            <x-td colspan="6" class="text-center" :last="true">
                                                No hay formas de pago
                                            </x-td>
                                        </x-tr>
                                    @endif
                                </x-slot>
                            </x-table>
                        </div>
                    </div>
                @endif
                <!-- End Sección forma de pago -->

                <!-- Sección recepción / entrega de documento -->
                @if ($number !== '14' && $number !== '07' && $number !== '11')
                    <div class="mt-6 border-t border-gray-300 px-4 pt-4 dark:border-gray-800">
                        <div class="flex items-center gap-4">
                            <h2
                                class="flex items-center gap-1 text-xl font-semibold text-primary-500 dark:text-primary-300">
                                <x-icon icon="info-circle" class="size-5" />
                                Recepción / entrega de documento
                            </h2>
                        </div>

                        @if ($number === '05' || $number === '06')
                            <div class="my-4 border-l-4 border-red-500 bg-red-100 p-4 text-sm text-red-700 dark:bg-red-950/50 dark:text-red-300"
                                role="alert">
                                <div class="flex justify-start gap-2">
                                    <x-icon icon="info-circle" class="h-5 w-5" />
                                    Información obligatoria, recepción entrega documento
                                </div>
                            </div>
                        @endif

                        <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                            <div class="flex-1">
                                <x-input type="text" name="" label="Número de documento"
                                    placeholder="Responsable de emitir el documento" />
                            </div>
                            <div class="flex-1">
                                <x-input type="text" label="Nombre" name=""
                                    placeholder="Responsable de emitir el documento" />
                            </div>
                        </div>
                        <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                            <div class="flex-1">
                                <x-input type="text" name="" label="Número documento"
                                    placeholder="Responsable de recibir el documento" />
                            </div>
                            <div class="flex-1">
                                <x-input type="text" label="Nombre" name=""
                                    placeholder="Responsable de recibir el documento" />
                            </div>
                        </div>
                    </div>
                @endif
                <!-- End Sección recepción / entrega de documento -->

            </div>
            <div class="mt-4 flex items-center justify-center">
                <x-button type="button" typeButton="primary" text="Generar documento" class="w-full sm:w-auto" />
            </div>
        </form>
    </section>

    <!-- Modal selected product -->
    <div id="selected-product" tabindex="-1" aria-hidden="true"
        class="fixed left-0 right-0 top-0 z-50 hidden max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
        <div class="relative mb-4 max-h-full w-full max-w-[750px] p-4">
            <!-- Modal content -->
            <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                <div class="flex flex-col">
                    <!-- Modal header -->
                    <div
                        class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Seleccionar producto
                        </h3>
                        <button type="button"
                            class="ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                            data-modal-hide="selected-product">
                            <x-icon icon="x" class="h-5 w-5" />
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4">
                        <x-table id="table-products">
                            <x-slot name="thead">
                                <x-tr>
                                    <x-th class="w-10">#</x-th>
                                    <x-th>Código</x-th>
                                    <x-th>Precio</x-th>
                                    <x-th>Descripción</x-th>
                                    <x-th :last="true"></x-th>
                                </x-tr>
                            </x-slot>
                            <x-slot name="tbody">
                                @foreach ($business_products as $product)
                                    <x-tr>
                                        <x-td>
                                            {{ $loop->iteration }}
                                        </x-td>
                                        <x-td>
                                            {{ $product->codigo }}
                                        </x-td>
                                        <x-td>
                                            ${{ $product->precioUni }}
                                        </x-td>
                                        <x-td>
                                            {{ $product->descripcion }}
                                        </x-td>
                                        <x-td :last="true">
                                            <form method="POST" action="{{ Route('business.dte.product.select') }}">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <x-button type="button" icon="arrow-next" size="small"
                                                    class="btn-selected-product" typeButton="secondary"
                                                    text="Seleccionar" />
                                            </form>
                                        </x-td>
                                    </x-tr>
                                @endforeach
                            </x-slot>
                        </x-table>
                        <div class="mt-4 hidden" id="container-data-product">
                            <form action="{{ Route('business.dte.product.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" id="product_id">
                                <div class="flex flex-col gap-4">
                                    <x-input type="text" label="Producto" name="product" readonly
                                        id="product_description" />
                                    <x-input type="number" name="precio" readonly id="product_price" label="Precio"
                                        icon="currency-dollar" placeholder="0.00" />
                                </div>
                                <div class="mt-4 flex gap-4">
                                    <div class="flex-1">
                                        <x-input type="number" label="Cantidad" name="cantidad" id="count"
                                            required />
                                    </div>
                                    <div class="flex-1">
                                        <x-select label="Tipo de venta" name="tipo" id="type-sale" required
                                            :options="[
                                                'Gravada' => ' Gravada',
                                                'Exenta ' => 'Exenta',
                                                'No sujeta' => 'No sujeta',
                                            ]" />
                                    </div>
                                    <div class="flex-1">
                                        <x-input type="number" name="descuento" id="descuento" label="Descuento"
                                            icon="currency-dollar" placeholder=0.00 />
                                    </div>
                                    <div class="flex-1">
                                        <x-input type="number" name="total" id="total_item" placeholder="0.00"
                                            label="Total" icon="currency-dollar" required />
                                    </div>
                                </div>
                                <div class="mt-4 flex items-center justify-center">
                                    <x-button type="button" typeButton="success" text="Añadir producto" icon="plus"
                                        class="w-full sm:w-auto" id="btn-add-product" />
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Modal footer -->
                    <div
                        class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                        <x-button type="button" text="Cancelar" icon="x" typeButton="secondary"
                            data-modal-hide="selected-product" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal selected product -->

    <!-- Drawer new product  -->
    <div id="drawer-new-product"
        class="fixed left-0 top-0 z-40 h-screen w-full -translate-x-full overflow-y-auto bg-white p-4 transition-transform dark:bg-gray-950 md:w-[650px]"
        tabindex="-1" aria-labelledby="drawer-label">
        <h5 id="drawer-label"
            class="mb-4 inline-flex items-center text-lg font-semibold text-gray-500 dark:text-gray-400">
            Nuevo producto o servicio
        </h5>
        <button type="button" data-drawer-hide="drawer-new-product" aria-controls="drawer-new-product"
            class="absolute end-2.5 top-2.5 flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white">
            <x-icon icon="x" class="h-5 w-5" />
            <span class="sr-only">Close menu</span>
        </button>
        <div class="my-4">
            <form action="">
                <div class="flex flex-col gap-4 sm:flex-row">
                    <div class="flex-[2]">
                        <x-select label="Tipo de producto" value="{{ old('tipo_producto') }}" required
                            selected="{{ old('tipo_producto') }}" name="tipo_producto" id="product_type"
                            :options="[
                                '1' => 'Bien',
                                '2' => 'Servicio',
                                '3' => 'Bien y servicio',
                            ]" />
                    </div>
                    <div class="flex-1">
                        <x-input type="number" label="Cantidad" name="" id="count_product" />
                    </div>
                    <div class="flex-[2]">
                        <x-select id="unidad_medida" required name="unidad_medida" :options="$unidades_medidas"
                            value="{{ old('unidad_medida') }}" selected="{{ old('unidad_medida') }}"
                            label="Unidad de medida" />
                    </div>
                </div>
                <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                    <div class="flex-[2]">
                        <x-input type="text" name="product" label="Producto" />
                    </div>
                    <div class="flex-1">
                        <x-select label="Tipo de venta" name="type_sale" id="type_sale" :options="['Gravada', 'Exenta', 'No sujeta']" />
                    </div>
                    <div class="flex-1">
                        <x-input type="number" icon="currency-dollar" id="price" placeholder="0.00"
                            label="Precio con IVA" name="price" />
                    </div>
                </div>
                <div class="mt-4 flex gap-4">
                    <div class="flex-1">
                        <x-input type="number" name="descuento" id="descuento_product" label="Descuento"
                            icon="currency-dollar" placeholder=0.00 />
                    </div>
                    <div class="flex-1">
                        <x-input type="number" name="total" id="total" placeholder="0.00" label="Total"
                            icon="currency-dollar" />
                    </div>
                </div>
                <div
                    class="mt-4 rounded-lg border border-dashed border-blue-500 bg-blue-100 p-4 text-blue-500 dark:bg-blue-950/30">
                    <ul class="flex flex-col gap-2 text-sm">
                        <li>
                            Impuesto al valor agregado (13%): <span class="font-semibold" id="iva">$0.00</span>
                        </li>
                        <li id="turismo-list" class="hidden">
                            Turismo: por alojamiento (5%): <span class="font-semibold" id="turismo">$0.00</span>
                        </li>
                        <li id="turismo-salida-pais-list" class="hidden">
                            Turismo: salida del país por vía áerea:
                            <span class="font-semibold">
                                $7.00
                            </span>
                        </li>
                        <li id="fovial-list" class="hidden">
                            FOVIAL ($0.20 por galón de combustible): <span class="font-semibold">$0.20</span>
                        </li>
                        <li id="cotrans-list" class="hidden">
                            COTRANS ($0.10 por galón de combustible): <span class="font-semibold">$0.10</span>
                        </li>
                        <li id="add-valorem-bebidas-alcoholicas-list" class="hidden">
                            Impuesto ad-valorem por diferencial de precio de bebidas alcohólicas (8%):
                            <span class="font-semibold" id="add-valorem-bebidas-alcoholicas">$0.00</span>
                        </li>
                        <li id="add-valorem-tabaco-cigarrillos-list" class="hidden">
                            Impuesto ad-valorem por diferencial de precio al tabaco cigarrillos (39%):
                            <span class="font-semibold" id="add-valorem-tabaco-cigarrillos">$0.00</span>
                        </li>
                        <li id="add-valorem-tabaco-cigarros-list" class="hidden">
                            Impuesto ad-valorem por diferencial de precio al tabaco cigarros (100%):
                            <span class="font-semibold" id="add-valorem-tabaco-cigarros">$0.00</span>
                        </li>
                    </ul>
                </div>
                <div class="mt-4 flex flex-col gap-2">
                    <x-input type="toggle" label="Impuesto al valor agregado (13%)" name="iva" checked
                        value="1" disabled />
                    <x-input type="toggle" label="Turismo: por alojamiento (5%)" name="turismo"
                        data-list="#turismo-list" class="tax-toggle" />

                    <x-input type="toggle" label="Turismo: salida del país por vía áerea" name="turismo_salida_pais"
                        id="turismo-salida-pais" class="tax-toggle" data-list="#turismo-salida-pais-list" />
                    <x-input type="toggle" label="FOVIAL ($0.20 por galón de combustible)" name="fovial"
                        id="fovial" class="tax-toggle" data-list="#fovial-list" />
                    <x-input type="toggle" label="COTRANS ($0.10 por galón de combustible)" name="cotrans"
                        id="cotrans" class="tax-toggle" data-list="#cotrans-list" />

                    <x-input type="toggle"
                        label="Impuesto ad-valorem por diferencial de precio de bebidas alcohólicas (8%)"
                        name="ad_valorem_bebidas_alcoholicas" data-list="#add-valorem-bebidas-alcoholicas-list"
                        class="tax-toggle" />
                    <x-input type="toggle"
                        label="Impuesto ad-valorem por diferencial de precio al tabaco cigarrillos (39%)"
                        name="ad_valorem_tabaco_cigarrillos" data-list="#add-valorem-tabaco-cigarrillos-list"
                        class="tax-toggle" />
                    <x-input type="toggle"
                        label="Impuesto ad-valorem por diferencial de precio al tabaco cigarros (100%)"
                        name="ad_valorem_tabaco_cigarros" data-list="#add-valorem-tabaco-cigarros-list"
                        class="tax-toggle" />
                </div>
                <div class="mt-4 flex items-center justify-center">
                    <x-button type="submit" typeButton="primary" text="Guardar producto" icon="save" />
                </div>
            </form>
        </div>
    </div>
    <!-- End Drawer new product  -->

    <!-- Modal add discount -->
    <div id="add-discount" tabindex="-1" aria-hidden="true"
        class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
        <div class="relative max-h-full w-full max-w-md p-4">
            <!-- Modal content -->
            <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                <div class="flex flex-col">
                    <!-- Modal header -->
                    <form action="{{ Route('business.dte.product.add-discounts') }}" method="POST">
                        @csrf
                        <div
                            class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Descuentos generales al resumen
                            </h3>
                            <button type="button"
                                class="ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                                data-modal-hide="add-discount">
                                <x-icon icon="x" class="h-5 w-5" />
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="flex flex-col gap-4 p-4">
                            <x-input type="number" label="Descuento a ventas gravadas" icon="currency-dollar"
                                placeholder="0.00" name="descuento_venta_gravada"
                                value="{{ $dte['descuento_venta_gravada'] ?? 0 }}" />
                            <x-input type="number" label="Descuento a ventas exentas" icon="currency-dollar"
                                placeholder="0.00" name="descuento_venta_exenta"
                                value="{{ $dte['descuento_venta_exenta'] ?? 0 }}" />
                            <x-input type="number" label="Descuento a ventas no sujetas" icon="currency-dollar"
                                placeholder="0.00" name="descuento_venta_no_sujeta"
                                value="{{ $dte['descuento_venta_no_sujeta'] ?? 0 }}" />
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                            <x-button type="button" text="Cancelar" icon="x" typeButton="secondary"
                                data-modal-hide="add-discount" />
                            <x-button type="button" class="btn-add-discounts" text="Aplicar descuento" icon="save"
                                typeButton="primary" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal add discount -->

    <!-- Modal selected customers -->
    <div id="selected-customer" tabindex="-1" aria-hidden="true"
        class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
        <div class="relative max-h-full w-full max-w-[750px] p-4">
            <!-- Modal content -->
            <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                <div class="flex flex-col">
                    <!-- Modal header -->
                    <div
                        class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Seleccionar cliente
                        </h3>
                        <button type="button"
                            class="btn-hide-selected-customer ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white">
                            <x-icon icon="x" class="h-5 w-5" />
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4">
                        <x-table id="table-customers">
                            <x-slot name="thead">
                                <x-tr>
                                    <x-th class="w-10">#</x-th>
                                    <x-th>Identificación</x-th>
                                    <x-th>Nombre</x-th>
                                    <x-th :last="true"></x-th>
                                </x-tr>
                            </x-slot>
                            <x-slot name="tbody">
                                @foreach ($business_customers as $customer)
                                    <x-tr>
                                        <x-td>{{ $loop->iteration }}</x-td>
                                        <x-td>
                                            {{ $customer->numDocumento }}
                                        </x-td>
                                        <x-td>
                                            {{ $customer->nombre }}
                                        </x-td>
                                        <x-td :last="true">
                                            <x-button type="button" icon="arrow-next" size="small"
                                                typeButton="secondary" text="Seleccionar" class="selected-customer"
                                                data-url="{{ Route('business.customers.show', $customer->id) }}" />
                                        </x-td>
                                    </x-tr>
                                @endforeach
                            </x-slot>
                        </x-table>
                    </div>
                    <!-- Modal footer -->
                    <div
                        class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                        <x-button type="button" text="Cancelar" icon="x" typeButton="secondary"
                            class="btn-hide-selected-customer" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal selected customers -->

    <!-- Modal add otros-documentos-asociados -->
    <div id="add-otros-documentos-asociados" tabindex="-1" aria-hidden="true"
        class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
        <div class="relative max-h-full w-full max-w-lg p-4">
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
                            class="ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                            data-modal-hide="add-otros-documentos-asociados">
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
                                'Emisor' => 'Emisor',
                                'Receptor' => 'Receptor',
                                'Médico' => 'Médico',
                            ]" name="documento_asociado" id="documento_asociado"
                                label="Documento asociado" />
                        </div>
                        <div id="container-data-documento-asociado">
                            <div class="mt-4 flex flex-col gap-4">
                                <x-input type="text" label="Identifación del documento"
                                    placeholder="Identificación del nombre del documento asociado"
                                    name="identificacion_documento" id="identificacion_documento" />
                                <x-input type="text" label="Descripción del documento"
                                    placeholder="Descripción de datos importantes del documento asociado"
                                    name="descripcion_documento" id="descripcion_documento" />
                            </div>
                        </div>
                        <div class="mt-4 hidden" id="container-data-medico">
                            <div class="flex flex-col gap-4">
                                <x-select :options="[
                                    'Cirugía' => 'Cirugía',
                                    'Operación' => 'Operación',
                                    'Tratamiento médico' => 'Tratamiento médico',
                                    'Cirugía Instituto Salvadoreño del Bienestar Magisterial' =>
                                        'Cirugía Instituto Salvadoreño del Bienestar Magisterial',
                                    'Operación Instituto Salvadoreño de Bienestar Magisterial' =>
                                        'Operación Instituto Salvadoreño de Bienestar Magisterial',
                                    'Tratamiento Médico Instituto Salvadoreño de Bienestar Magisterial' =>
                                        'Tratamiento Médico Instituto Salvadoreño de Bienestar Magisterial',
                                ]" name="tipo_servicio" id="tipo_servicio"
                                    label="Tipo de servicio" />
                                <x-input type="text" label="Nombre" placeholder="Nombre del médico"
                                    name="nombre_medico" id="nombre_medico" />
                                <div class="flex items-center gap-4">
                                    <div class="flex-1">
                                        <x-select :options="[
                                            'DUI' => 'DUI',
                                            'Otro' => 'Otro',
                                        ]" name="tipo_documento_doctor" id="tipo_documento"
                                            label="Tipo de documento" />
                                    </div>
                                    <div class="flex-1">
                                        <x-input type="text" label="NIT" placeholder="Número de NIT"
                                            name="nit_doctor" id="nit_doctor" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal footer -->
                    <div
                        class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                        <x-button type="button" text="Cancelar" icon="x" typeButton="secondary"
                            data-modal-hide="add-otros-documentos-asociados" />
                        <x-button type="submit" text="Agregar" icon="save" typeButton="primary" />
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Modal add otros-documentos-asociados -->

    <!-- Modal add documento fisico -->
    <div id="add-documento-fisico" tabindex="-1" aria-hidden="true"
        class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
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
                            class="ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                            data-modal-hide="add-documento-fisico">
                            <x-icon icon="x" class="h-5 w-5" />
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="flex flex-col gap-4 p-4">
                        <span class="flex items-center gap-1 text-base font-semibold text-gray-500 dark:text-gray-300">
                            <x-icon icon="info-circle" class="size-5" />
                            Datos del documento fisico
                        </span>
                        <input type="hidden" name="tipo_generacion" value="Físico" />
                        <x-select :options="[
                            'Comprobante de crédito fiscal' => 'Comprobante de crédito fiscal',
                            'Comprobante de retención' => 'Comprobante de retención',
                        ]" name="tipo_documento" id="tipo_documento_fisico"
                            label="Tipo de documento" />
                        <div class="flex flex-col items-center gap-4 sm:flex-row">
                            <div class="flex-1">
                                <x-input type="text" label="Número de documento"
                                    placeholder="Ingresar el número del documento" name="numero_documento" />
                            </div>
                            <div class="flex-1">
                                <x-input type="date" label="Fecha de documento" name="fecha_documento" />
                            </div>
                        </div>

                        @if ($number === '07')
                            <x-input type="textarea" label="Descripción" name="descripcion_retencion"
                                id="descripción_document" placeholder="Ingresar la descripción del documento" />
                            <x-input type="number" name="monto_sujeto_retencion" label="Monto sujeto a retención"
                                icon="currency-dollar" id="monto_sujeto_retencion" step="0.01" />
                            <div class="flex flex-col items-center gap-4 sm:flex-row">
                                <div class="flex-[2]">
                                    <x-select label="Código de tributo" :options="[
                                        'Retención IVA' => 'Retención IVA',
                                        'Otras retenciones IVA (casos especiales)' =>
                                            'Otras retenciones IVA (casos especiales)',
                                    ]" id="codigo_tributo"
                                        name="codigo_tributo" />
                                </div>
                                <div class="flex-1">
                                    <x-input type="number" name="iva_retenido" id="iva_retenido"
                                        label="Monto de IVA" placeholder="0.00" step="0.01"
                                        icon="currency-dollar" />
                                </div>
                            </div>
                        @endif
                    </div>
                    <!-- Modal footer -->
                    <div
                        class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                        <x-button type="button" text="Cancelar" icon="x" typeButton="secondary"
                            data-modal-hide="add-documento-fisico" />
                        <x-button type="submit" text="Agregar" icon="save" typeButton="primary" />
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Modal add add documento fisico -->

    <!-- Modal add documento electronico -->
    <div id="add-documento-electronico" tabindex="-1" aria-hidden="true"
        class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
        <div class="relative max-h-full w-full max-w-2xl p-4">
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
                            class="ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                            data-modal-hide="add-documento-electronico">
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
                                <x-input type="text" label="NIT del receptor" name="nit_receptor"
                                    placeholder="Ingresar el NIT del receptor" />
                            </div>
                            <div class="flex-1">
                                <x-select :options="['Comprobante de crédito fiscal', 'Comprobante de retención']" name="tipo_documento" id="tipo_documento"
                                    label="Tipo de documento" />
                            </div>
                        </div>
                        <div class="flex flex-col gap-4 sm:flex-row">
                            <div class="flex-1">
                                <x-input type="date" label="Fecha emitido desde" name="" />
                            </div>
                            <div class="flex-1">
                                <x-input type="date" label="Fecha emitido hasta" name="" />
                            </div>
                        </div>
                        <div class="mt-4">
                            <x-button type="button" typeButton="primary" text="Consultar" icon="search" />
                        </div>
                    </div>
                    <div class="px-4 pb-4">
                        <span
                            class="mb-2 flex items-center gap-1 text-base font-semibold text-gray-500 dark:text-gray-300">
                            <x-icon icon="info-circle" class="size-5" />
                            Resultados de la consulta
                        </span>
                        <x-table :datatable="false">
                            <x-slot name="thead">
                                <x-tr>
                                    <x-th>Fecha de emisión</x-th>
                                    <x-th>Código de generación</x-th>
                                    <x-th>Monto</x-th>
                                    <x-th></x-th>
                                </x-tr>
                            </x-slot>
                            <x-slot name="tbody">
                                <x-tr :last="true">
                                    <x-td>2021-10-01</x-td>
                                    <x-td>0001</x-td>
                                    <x-td>$0.00</x-td>
                                    <x-td :last="true">
                                        <x-button type="button" icon="arrow-next" size="small"
                                            typeButton="secondary" text="Seleccionar" />
                                    </x-td>
                                </x-tr>
                            </x-slot>
                        </x-table>
                    </div>
                    <!-- Modal footer -->
                    <div
                        class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                        <x-button type="button" text="Cancelar" icon="x" typeButton="secondary"
                            data-modal-hide="add-documento-electronico" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal add add documento electronico -->

    <div id="cancel-dte" tabindex="-1" aria-hidden="true"
        class="deleteModal fixed inset-0 left-0 right-0 top-0 z-[100] hidden h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50">
        <div class="relative flex h-full w-full max-w-md items-center justify-center p-4 md:h-auto">
            <!-- Modal content -->
            <div
                class="motion-preset-expand relative w-full rounded-lg bg-white text-center shadow motion-duration-300 dark:bg-gray-900">
                <div class="p-4">
                    <button type="button"
                        class="closeModal absolute right-2.5 top-2.5 ml-auto inline-flex items-center rounded-lg bg-transparent p-1.5 text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-800 dark:hover:text-white"
                        data-modal-toggle="cancel-dte">
                        <x-icon icon="x" class="h-5 w-5" />
                        <span class="sr-only">Close modal</span>
                    </button>
                    <span
                        class="mx-auto mb-4 flex w-max items-center justify-center rounded-full bg-red-100 p-4 text-red-500 dark:bg-red-900/30">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" class="size-12"
                            viewBox="0 0 24 24" fill="currentColor"
                            class="icon icon-tabler icons-tabler-filled icon-tabler-alert-triangle">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path
                                d="M12 1.67c.955 0 1.845 .467 2.39 1.247l.105 .16l8.114 13.548a2.914 2.914 0 0 1 -2.307 4.363l-.195 .008h-16.225a2.914 2.914 0 0 1 -2.582 -4.2l.099 -.185l8.11 -13.538a2.914 2.914 0 0 1 2.491 -1.403zm.01 13.33l-.127 .007a1 1 0 0 0 0 1.986l.117 .007l.127 -.007a1 1 0 0 0 0 -1.986l-.117 -.007zm-.01 -7a1 1 0 0 0 -.993 .883l-.007 .117v4l.007 .117a1 1 0 0 0 1.986 0l.007 -.117v-4l-.007 -.117a1 1 0 0 0 -.993 -.883z" />
                        </svg>
                    </span>
                    <p class="mb-4 text-gray-500 dark:text-gray-300">
                        ¿Estás seguro de cancelar la generación de este documento?
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Perderás toda la información ingresada hasta el momento.
                    </p>
                </div>
                <div class="flex items-center justify-center space-x-4 py-4">
                    <x-button type="button" data-modal-toggle="cancel-dte" class="closeModal" text="No, continuar"
                        icon="x" typeButton="secondary" />
                    <x-button type="a" href="{{ Route('business.dte.cancel') }}" class="confirmDelete"
                        text="Sí, cancelar" icon="trash" typeButton="danger" />
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/dte.js')
@endpush
