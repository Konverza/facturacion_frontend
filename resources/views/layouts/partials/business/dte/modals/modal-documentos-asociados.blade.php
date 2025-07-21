<!-- Modal add otros-documentos-asociados -->
<div id="add-otros-documentos-asociados" tabindex="-1" aria-hidden="true"
    class="fixed left-0 right-0 top-0 z-[100] hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
    <div class="relative max-h-full w-full max-w-xl p-4">
        <!-- Modal content -->
        <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
            <form action="{{ Route('business.dte.associated-documents.store') }}" method="POST" class="flex flex-col">
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
                        <x-select :options="($number == 15)
                            ? [
                                '1' => 'Emisor',
                                '2' => 'Receptor',
                            ]
                            : [
                                '1' => 'Emisor',
                                '2' => 'Receptor',
                                '3' => 'Médico',
                            ]"
                            name="documento_asociado"
                            id="documento_asociado"
                            label="Documento asociado"
                            :search="false"
                            required
                        />
                    </div>
                    <div id="container-data-documento-asociado">
                        <div class="mt-4 flex flex-col gap-4">
                            <x-input type="text" label="Identifación del documento"
                                placeholder="Identificación del nombre del documento asociado"
                                name="identificacion_documento" id="identificacion_documento" required />
                            <x-input type="text" label="Descripción del documento"
                                placeholder="Descripción de datos importantes del documento asociado"
                                name="descripcion_documento" id="descripcion_documento" required />
                        </div>
                    </div>
                    <div class="mt-4 hidden" id="container-data-medico">
                        <div class="flex flex-col gap-4">
                            <x-select :options="$tipo_servicio" name="tipo_servicio" id="tipo_servicio" label="Tipo de servicio"
                                :search="false" required />
                            <x-input type="text" label="Nombre" placeholder="Nombre del médico" name="nombre_medico"
                                id="nombre_medico" required />
                            <div class="flex flex-col items-center gap-4 sm:flex-row">
                                <div class="flex-1">
                                    <x-select :options="[
                                        '1' => 'NIT',
                                        '2' => 'DUI',
                                        '3' => 'Otro',
                                    ]" name="tipo_documento_doctor" id="tipo_documento"
                                        label="Tipo de documento" :search="false" required />
                                </div>
                                <div class="flex-1">
                                    <x-input type="text" label="Número de documento" required
                                        placeholder="Número de documento" name="numero_doc" id="numero_doc" />
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
                    <x-button type="button" class="submit-form" text="Agregar" icon="plus" typeButton="primary" />
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End Modal add otros-documentos-asociados -->
