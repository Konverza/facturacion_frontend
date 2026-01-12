@extends('layouts.auth-template')
@section('title', 'Reportería Contable')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full justify-between">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Reportería Contable
            </h1>
        </div>
        <div class="mt-4 pb-4">
            <div class="my-2 flex flex-col gap-2 overflow-hidden rounded-lg border-2 border-dashed border-blue-300 bg-blue-50 p-4 text-center dark:border-blue-800 dark:bg-blue-950/40"
                role="alert">
                <div class="flex justify-start gap-2 text-sm font-semibold text-blue-800 dark:text-blue-200">
                    <x-icon icon="info-circle" class="h-5 w-5" />
                    Nota:
                </div>
                <p class="text-left text-sm text-blue-700 dark:text-blue-300">
                    Para descargar los Anexos para el F07 en formato CSV, seleccionar en <b>"Tipo de Reporte"</b> la opción
                    <b>"Anexos F07"</b>.<br>
                    Para los libros en Excel, seleccionar el tipo de libro que se desea generar.
                </p>
            </div>
            <div class="flex flex-col gap-2 overflow-hidden rounded-lg border-2 border-dashed border-yellow-300 bg-yellow-50 p-4 text-center dark:border-yellow-800 dark:bg-yellow-950/40"
                role="alert">
                <div class="flex justify-start gap-2 text-sm font-semibold text-yellow-800 dark:text-yellow-200">
                    <x-icon icon="warning" class="h-5 w-5" />
                    Importante
                </div>
                <p class="text-left text-sm text-yellow-700 dark:text-yellow-300">
                    Aunque el sistema de Konverza realiza inferencias automáticas para facilitar el llenado de los
                    anexos, <b class="underline">la revisión y validación final de todos los valores es
                        responsabilidad exclusiva del contador encargado de la declaración</b>. Una clasificación
                    incorrecta o un valor mal asignado puede afectar la base imponible, generar inconsistencias en
                    la información reportada o provocar rechazos automáticos por parte del Ministerio de Hacienda.
                    Por ello, <b>es fundamental que cada dato sea verificado cuidadosamente</b>, especialmente
                    aquellos relacionados con la naturaleza tributaria de la operación o la clasificación del
                    ingreso/gasto, antes de proceder con el envío del anexo.
                </p>
            </div>
            <form action="{{ Route('business.reporting.store') }}"
                class="flex mt-3 items-start gap-4 md:flex-row flex-col-reverse" method="POST" enctype="multipart/form-data"
                id="form-generate-book">
                @csrf
                <div class="flex flex-[2] flex-col gap-4">
                    <x-select :options="[
                        'anexos_f07' => 'Anexos F07',
                        'contribuyentes' => 'Libro de ventas a contribuyentes',
                        'consumidores' => 'Libro de ventas a consumidores',
                        // 'compras' => 'Libro de compras',
                        // 'venta_tercero' => 'Libro de Ventas Gravadas por cuenta de terceros domiciliados',
                        // 'compras_se' => 'Libro de Compras a Sujetos Excluidos',
                        // 'retencion_iva' => 'Retención de IVA 1% (emitidos)',
                        // 'percepcion_iva' => 'Percepción de IVA (recibidos)',
                    ]" name="book_type" id="book-type" label="Tipo de Reporte" :search="false"
                        required />
                    <div class="container-books hidden" id="container-tipo-anexo">
                        <x-select :options="[
                            'contribuyentes' => 'Detalle de Ventas al Contribuyente',
                            'consumidores' => 'Detalle de Ventas al Consumidor Final',
                            // 'compras' => 'Detalle de Compras',
                            'compras_se' => 'Detalle de Compras a Sujetos Excluidos (Casilla 66)',
                            // 'venta_tercero' => 'Detalle de Ventas por cuenta de terceros domiciliados (Casilla 108)',
                        ]" name="tipo_anexo" id="anexo-type" label="Tipo de Anexo" />
                    </div>
                    <div class="flex w-full gap-4 sm:flex-row flex-col">
                        <div class="flex-1">
                            <x-input type="date" name="start_date" label="Fecha de inicio" />
                        </div>
                        <div class="flex-1">
                            <x-input type="date" name="end_date" label="Fecha de fin" />
                        </div>
                    </div>
                    <div class="flex w-full gap-4 flex-col container-anexos hidden" id="container-tipo-operacion-ingreso">
                        <x-select id="tipo_operacion" :options="[
                            '1' => 'Gravada',
                            '2' => 'No Gravada o Exento',
                            '3' => 'Excluido o no Constituye Renta',
                            '4' => 'Mixta',
                            '12' => 'Ingresos que ya fueron sujetos de retención informados',
                            '13' => 'Sujetos pasivos Excluidos (art. 6 LISR)',
                        ]" label="Tipo de Operación" name="tipo_operacion"
                            required />
                        <x-select id="tipo_ingreso" :options="[
                            '1' => 'Profesiones, Artes y Oficios',
                            '2' => 'Actividades de Servicios',
                            '3' => 'Actividades Comerciales',
                            '4' => 'Actividades Industriales',
                            '5' => 'Actividades Agropecuarias',
                            '6' => 'Utilidades y Dividendos',
                            '7' => 'Exportaciones de bienes',
                            '8' => 'Servicios Realizados en el Exterior y Utilizados en El Salvador',
                            '9' => 'Exportaciones de servicios',
                            '10' => 'Otras Rentas Gravables',
                            '12' => 'Ingresos que ya fueron sujetos de retención informados',
                            '13' => 'Sujetos pasivos Excluidos (art. 6 LISR)',
                        ]" label="Tipo de Ingreso" name="tipo_ingreso"
                            required />
                    </div>
                    <div class="flex w-full gap-4 flex-col container-anexos hidden" id="container-tipo-operacion-egreso">
                        <x-select id="tipo_operacion_se" :options="[
                            '1' => 'Gravada',
                            '2' => 'No Gravada o Exenta',
                            '3' => 'Excluido o no Constituye Renta',
                            '4' => 'Mixta',
                        ]" label="Tipo de Operación" name="tipo_operacion_se"
                            required />
                        <p class="text-xs -mt-2 text-gray-500 dark:text-gray-200">Para el Anexo "Compras a Sujetos
                            Excluidos" siempre se asignará "No Gravada o Exenta"</p>
                        <x-select id="clasificacion" :options="[
                            '1' => 'Costo',
                            '2' => 'Gasto',
                        ]" label="Clasificación" name="clasificacion"
                            required />
                        <x-select id="sector" :options="[
                            '1' => 'Industrial',
                            '2' => 'Comercial',
                            '3' => 'Agropecuario',
                            '4' => 'Servicios/Otros',
                        ]" label="Sector" name="sector" required />
                        <x-select id="tipo_costo" :options="[
                            '1' => 'Gastos de Venta sin Donación',
                            '2' => 'Gastos de Administración sin Donación',
                            '3' => 'Gastos Financieros sin Donación',
                            '4' => 'Costo Artículos Importados/Internaciones',
                            '5' => 'Costo Artículos Internos',
                            '6' => 'Costos Indirectos de Fabricación',
                            '7' => 'Mano de Obra',
                        ]" label="Tipo de Costo / Gasto" name="tipo_costo"
                            required />
                    </div>
                    <div class="flex flex-col gap-2">
                        <div id="selected-documents" class="hidden">
                            <div class="flex flex-col gap-2">
                                <x-input type="toggle" name="only_selected"
                                    info="El libro contable solo se generara con la información de los libros que sean adjuntando en el formulario"
                                    label="Generar solo con documentos adjuntados" id="only-selected" value="1" />
                                <x-input type="toggle" name="only_mix" :checked="true"
                                    info="El libro contable se generara con los documentos adjuntandos más los documentos que estén en el sistema"
                                    label="Generar con los documentos adjuntados más los documentos ingresados en el sistema"
                                    id="only-mix" value="1" />
                            </div>
                        </div>
                        {{-- <x-input type="toggle" name="format_csv" label="Generar en formato .CSV" /> --}}
                    </div>

                    <div id="container-ventas-contribuyentes" class="container-books hidden">
                        <div class="flex flex-col gap-2 overflow-hidden rounded-lg border-2 border-dashed border-blue-300 bg-blue-50 p-4 text-center dark:border-blue-800 dark:bg-blue-950/40"
                            role="alert">
                            <div class="flex justify-start gap-2 text-sm font-semibold text-blue-800 dark:text-blue-200">
                                <x-icon icon="info-circle" class="h-5 w-5" />
                                Libro de ventas a contribuyentes
                            </div>
                            <p class="text-left text-sm text-blue-700 dark:text-blue-300">
                                Para el libro de ventas a contribuyentes, solo se aceptarán los documentos: <b>Comprobante
                                    de crédito fiscal</b>, <b>Nota de crédito</b> y <b>Nota de débito</b>.
                            </p>
                        </div>
                    </div>

                    <div id="container-ventas-consumidor-final" class="container-books hidden">
                        <div class="flex flex-col gap-2 overflow-hidden rounded-lg border-2 border-dashed border-blue-300 bg-blue-50 p-4 text-center dark:border-blue-800 dark:bg-blue-950/40"
                            role="alert">
                            <div class="flex justify-start gap-2 text-sm font-semibold text-blue-800 dark:text-blue-200">
                                <x-icon icon="info-circle" class="h-5 w-5" />
                                Libro de ventas a consumidor final
                            </div>
                            <p class="text-left text-sm text-blue-700 dark:text-blue-300">
                                Para el libro de ventas a consumidor final, solo se aceptarán los documentos:
                                <b>Factura</b> y <b>Factura de exportación</b>.
                            </p>
                        </div>
                    </div>

                    <div id="container-retencion-iva" class="container-books hidden">
                        <div class="flex flex-col gap-2 overflow-hidden rounded-lg border-2 border-dashed border-blue-300 bg-blue-50 p-4 text-center dark:border-blue-800 dark:bg-blue-950/40"
                            role="alert">
                            <div class="flex justify-start gap-2 text-sm font-semibold text-blue-800 dark:text-blue-200">
                                <x-icon icon="info-circle" class="h-5 w-5" />
                                Retención IVA (1%)
                            </div>
                            <p class="text-left text-sm text-blue-700 dark:text-blue-300">
                                Para el libro de retención de IVA (1%), solo se aceptarán los documentos:
                                <b>Nota de crédito</b>, <b>Nota de débito</b> y <b>Comprobante de retención</b>.
                            </p>
                        </div>
                    </div>

                    <div id="container-compras" class="container-books hidden">
                        <div class="flex flex-col gap-2 overflow-hidden rounded-lg border-2 border-dashed border-blue-300 bg-blue-50 p-4 text-center dark:border-blue-800 dark:bg-blue-950/40"
                            role="alert">
                            <div class="flex justify-start gap-2 text-sm font-semibold text-blue-800 dark:text-blue-200">
                                <x-icon icon="info-circle" class="h-5 w-5" />
                                Libro de compras
                            </div>
                            <p class="text-left text-sm text-blue-700 dark:text-blue-300">
                                Para el libro de compras, solo se aceptarán los documentos:
                                <b>Comprobante de crédito fiscal</b>, <b>Nota de crédito</b>, <b>Nota de débito</b>, y
                                <b>Factura de exportación</b>, que fueron emitidos por tus proveedores (documentos
                                recibidos).
                            </p>
                        </div>
                    </div>

                    <div id="container-percepcion-iva" class="container-books hidden">
                        <div class="flex flex-col gap-2 overflow-hidden rounded-lg border-2 border-dashed border-blue-300 bg-blue-50 p-4 text-center dark:border-blue-800 dark:bg-blue-950/40"
                            role="alert">
                            <div class="flex justify-start gap-2 text-sm font-semibold text-blue-800 dark:text-blue-200">
                                <x-icon icon="info-circle" class="h-5 w-5" />
                                Percepción IVA (1%)
                            </div>
                            <p class="text-left text-sm text-blue-700 dark:text-blue-300">
                                Para el libro de percepción de IVA (1%), solo se aceptarán los documentos:
                                <b>Comprobante de crédito fiscal</b>, <b>Nota de crédito</b>, y <b>Nota de débito</b>, que
                                fueron emitidos por tus proveedores (documentos
                                recibidos).
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-center gap-4">
                        <x-button type="submit" text="Generar documento" typeButton="primary"
                            class="w-full sm:w-auto" />
                    </div>
                </div>
                <div class="flex-1">
                    <!-- Zona de arrastrar y soltar archivos -->

                    <p class="mb-4 text-sm font-semibold text-secondary-500 dark:text-secondary-400">
                        Adjunta los archivos JSON o ZIP que contienen los documentos electrónicos (emitidos o
                        recibidos). Puedes subir múltiples archivos a la vez.
                    </p>

                    <div id="drop-zone"
                        class="group relative cursor-pointer rounded-lg border-2 border-dashed border-secondary-300 p-8 text-center transition-all duration-300 hover:border-secondary-400 hover:bg-secondary-100 dark:border-secondary-700 dark:hover:bg-secondary-950">
                        <div id="drop-content" class="flex flex-col items-center justify-center space-y-4">
                            <div
                                class="rounded-lg bg-secondary-200 p-4 transition-colors group-hover:bg-secondary-300 dark:bg-secondary-800 dark:group-hover:bg-secondary-900">
                                <x-icon icon="cloud-upload"
                                    class="size-12 text-secondary-800 transition-colors dark:text-secondary-400" />
                            </div>

                            <div class="space-y-2">
                                <h3 id="drop-title" class="text-lg font-semibold text-secondary-900 dark:text-white">
                                    Arrastra y suelta tus archivos aquí
                                </h3>
                                <p class="text-sm text-secondary-600 dark:text-secondary-400">
                                    o <button type="button" id="browse-files"
                                        class="font-medium text-blue-500 underline hover:text-blue-600">
                                        haz clic para seleccionar
                                    </button>
                                </p>
                                <p class="text-xs text-secondary-500 dark:text-secondary-500">
                                    Formatos soportados: JSON, ZIP
                                </p>
                            </div>
                        </div>
                        <input type="file" id="file-input" multiple accept=".json,.zip" class="hidden">
                    </div>
                    <div id="files-list" class="mt-4 hidden space-y-2">
                        <h4 class="text-sm font-medium text-secondary-900 dark:text-white">
                            Archivos seleccionados:
                        </h4>
                        <div id="files-container" class="max-h-[500px] space-y-2 overflow-y-auto">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
    @vite('resources/js/reporting.js')
@endpush
