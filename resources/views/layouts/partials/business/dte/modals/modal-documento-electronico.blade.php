<!-- Modal add documento electronico -->

@php
    $tipos_documentos = [];
    switch ($number) {
        case '01':
            $tipos_documentos = [
                '04' => 'Nota de remisión',
                '09' => 'Documento contable de liquidación',
            ];
            break;
        case '03':
            $tipos_documentos = [
                '04' => 'Nota de remisión',
                '08' => 'Comprobante de liquidación',
                '09' => 'Documento contable de liquidación',
            ];
            break;
        case '04':
            $tipos_documentos = [
                '01' => 'Factura Consumidor Final',
                '03' => 'Comprobante de crédito fiscal',
            ];
            break;
        case '05':
        case '06':
            $tipos_documentos = [
                '03' => 'Comprobante de crédito fiscal',
                '07' => 'Comprobante de retención',
            ];
            break;
    }
@endphp

<div id="add-documento-electronico" tabindex="-1" aria-hidden="true"
    class="fixed left-0 right-0 top-0 z-[100] hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
    <div class="relative m-4 mb-8 max-h-full w-full max-w-[950px]">
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
                    @php
                        $documento_receptor = isset($dte['customer']) ? $dte['customer']['numDocumento'] : null;
                    @endphp
                    @livewire('business.tables.documentos-electronicos-table', ['nit' => $business->nit, 'number' => $number, 'numero_documento' => $documento_receptor])
                    {{-- <span class="flex items-center gap-1 text-base font-semibold text-gray-500 dark:text-gray-300">
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
                            <x-select :options="$tipos_documentos" name="tipo_documento" id="tipo_documento"
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
                    <span class="mb-2 flex items-center gap-1 text-base font-semibold text-gray-500 dark:text-gray-300">
                        <x-icon icon="info-circle" class="size-5" />
                        Resultados de la consulta
                    </span>
                    <div id="container-table-dtes">
                    </div>
                </div> --}}
                <!-- Modal footer -->
                <div
                    class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                    <x-button type="button" class="hide-modal" text="Cancelar" icon="x" typeButton="secondary"
                        data-target="#add-documento-electronico" />
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Modal add add documento electronico -->
