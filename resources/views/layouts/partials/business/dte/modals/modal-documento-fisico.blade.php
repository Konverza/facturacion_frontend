<!-- Modal add documento fisico -->

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

<div id="add-documento-fisico" tabindex="-1" aria-hidden="true"
    class="fixed left-0 right-0 top-0 z-[100] hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
    <div class="relative mb-4 max-h-full w-full max-w-xl p-4">
        <!-- Modal content -->
        <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
            <form action="{{ Route('business.dte.related-documents.store') }}" method="POST" class="flex flex-col">
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
                    <input type="hidden" name="tipo_generacion" value="1" />
                    <x-select :options="$tipos_documentos" name="tipo_documento" id="tipo_documento_fisico"
                        label="Tipo de documento" :search="false" required />
                    <div class="flex flex-col items-center gap-4 sm:flex-row">
                        <div class="w-full flex-1">
                            <x-input type="number" min="1" label="Número de documento" required
                                placeholder="Ingresar el número del documento" name="numero_documento" />
                        </div>
                        <div class="w-full flex-1">
                            <x-input type="date" label="Fecha de documento" name="fecha_documento" required />
                        </div>
                    </div>
                </div>
                <!-- Modal footer -->
                <div
                    class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                    <x-button type="button" class="hide-modal" text="Cancelar" icon="x" typeButton="secondary"
                        data-target="#add-documento-fisico" />
                    <x-button type="button" class="submit-form" text="Agregar" icon="plus" typeButton="primary" />
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End Modal add add documento fisico -->
