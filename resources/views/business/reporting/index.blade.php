@extends('layouts.auth-template')
@section('title', 'Reportería')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full justify-between">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Reportería
            </h1>
        </div>
        <div class="mt-4 pb-4">
            <form action="{{ Route('business.reporting.store') }}"
                class="flex items-start gap-4 md:flex-row flex-col-reverse" method="POST" enctype="multipart/form-data"
                id="form-generate-book">
                @csrf
                <div class="flex flex-[2] flex-col gap-4">
                    <x-select :options="[
                        'contribuyentes' => 'Libro de ventas a contribuyentes',
                        'consumidores' => 'Libro de ventas a consumidores',
                        'retencion_iva' => 'Retención de IVA 1% (emitidos)',
                        // 'compras' => 'Libro de compras',
                        // 'percepcion_iva' => 'Percepción de IVA (recibidos)',
                    ]" name="book_type" id="book-type" label="Tipo de Reporte" :search="false"
                        required />
                    <div class="flex w-full gap-4 sm:flex-row flex-col">
                        <div class="flex-1">
                            <x-input type="date" name="start_date" label="Fecha de inicio" />
                        </div>
                        <div class="flex-1">
                            <x-input type="date" name="end_date" label="Fecha de fin" />
                        </div>
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

                    <div class="flex flex-col gap-2 overflow-hidden rounded-lg border-2 border-dashed border-blue-300 bg-blue-50 p-4 text-center dark:border-blue-800 dark:bg-blue-950/40"
                        role="alert">
                        <div class="flex justify-start gap-2 text-sm font-semibold text-blue-800 dark:text-blue-200">
                            <x-icon icon="info-circle" class="h-5 w-5" />
                            Importante
                        </div>
                        <p class="text-left text-sm text-blue-700 dark:text-blue-300">
                            El libro de ventas a contribuyentes, compras de consumidor final y el de retención de IVA (1%)
                            son para los <b>documentos emitidos</b>, el libro de compras y percepción de IVA (1%) son para
                            los <b>documentos recibidos</b>. Puedes adjuntar más archivos.
                        </p>
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
                        <x-button type="submit" text="Generar documento" typeButton="primary" class="w-full sm:w-auto" />
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
