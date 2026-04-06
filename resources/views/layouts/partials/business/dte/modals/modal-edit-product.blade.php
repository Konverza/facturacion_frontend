@php
    $unidadesMedidas = $unidades_medidas ?? [];
@endphp

<div id="edit-product-modal" tabindex="-1" aria-hidden="true"
    class="fixed left-0 right-0 top-0 z-50 hidden max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
    <div class="relative mb-4 max-h-full w-full max-w-3xl p-4">
        <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
            <div class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Editar item</h3>
                <button type="button" data-target="#edit-product-modal"
                    class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white">
                    <x-icon icon="x" class="h-5 w-5" />
                </button>
            </div>

            <form id="form-edit-product" action="#" method="POST" class="p-4">
                @csrf
                <input type="hidden" name="item_id" id="edit_item_id">

                <div class="flex flex-col gap-4 p-4">
                    <x-input type="text" label="Producto" id="edit_descripcion_preview" readonly />
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <x-input type="number" label="Cantidad" name="cantidad" id="edit_cantidad" step="0.00000001"
                            min="0.00000001" required />

                        @if (($dte['type'] ?? null) !== '11' && ($dte['type'] ?? null) !== '14')
                            <x-select label="Tipo de venta" name="tipo" id="edit_tipo" :options="[
                                'Gravada' => 'Gravada',
                                'Exenta' => 'Exenta',
                                'No sujeta' => 'No sujeta',
                            ]" :search="false" required />
                        @else
                            <x-input type="hidden" name="tipo" id="edit_tipo_hidden" value="Gravada" />
                        @endif
                    </div>
                </div>

                <div id="edit-existing-note"
                    class="m-4 rounded-lg border border-dashed border-blue-500 bg-blue-100 p-4 text-sm text-blue-600 dark:bg-blue-950/30 dark:text-blue-300">
                    Para productos existentes solo se permite editar cantidad y tipo de venta.
                </div>

                <div id="edit-manual-fields" class="mt-4 p-4 hidden">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <x-select label="Tipo de producto" name="tipo_item" id="edit_tipo_item" :options="[
                            '1' => 'Bien',
                            '2' => 'Servicio',
                            '3' => 'Bien y servicio',
                        ]" :search="false" required />

                        <x-select label="Unidad de medida" name="unidad_medida" id="edit_unidad_medida"
                            :options="$unidadesMedidas" required />
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <x-input type="text" label="Descripción" name="descripcion" id="edit_descripcion" required />
                        <x-input type="number" label="Precio unitario" name="precio_unitario" id="edit_precio_unitario"
                            step="0.00000001" min="0" required />
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <x-input type="number" label="Descuento" name="descuento" id="edit_descuento"
                            step="0.00000001" min="0" />
                        <x-input type="number" label="Total" name="total" id="edit_total" step="0.00000001"
                            min="0" required />
                    </div>

                    @if (($dte['type'] ?? null) !== '11' && ($dte['type'] ?? null) !== '14')
                        <div class="mt-4">
                            <x-select label="Documento relacionado" name="documento_relacionado"
                                id="edit_documento_relacionado" :required="isset($dte['documentos_relacionados']) && count($dte['documentos_relacionados']) > 0"
                                :options="isset($dte['documentos_relacionados'])
                                    ? collect($dte['documentos_relacionados'])->mapWithKeys(function ($item) {
                                        return [
                                            $item['numero_documento'] =>
                                                $item['tipo_documento'] . ' - ' . $item['numero_documento'],
                                        ];
                                    })
                                    : []"
                                :search="false" />
                        </div>
                    @endif

                    <div class="mt-4 flex flex-col gap-2">
                        <input type="hidden" name="tributos[]" value="20" />
                        <x-input name="tributos[]" type="toggle" value="59"
                            label="Turismo: por alojamiento (5%)" class="edit-tributo-toggle" />
                        <x-input name="tributos[]" type="toggle" value="71"
                            label="Turismo: salida del país por vía áerea" class="edit-tributo-toggle" />
                        <x-input name="tributos[]" type="toggle" value="D1"
                            label="FOVIAL ($0.20 por galón de combustible)" class="edit-tributo-toggle" />
                        <x-input name="tributos[]" type="toggle" value="C8"
                            label="COTRANS ($0.10 por galón de combustible)" class="edit-tributo-toggle" />
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-300 p-4 dark:border-gray-800">
                    <x-button type="button" text="Cancelar" typeButton="secondary" class="hide-modal"
                        data-target="#edit-product-modal" />
                    <x-button type="submit" text="Guardar cambios" typeButton="primary" icon="save"
                        class="submit-edit-item" />
                </div>
            </form>
        </div>
    </div>
</div>
