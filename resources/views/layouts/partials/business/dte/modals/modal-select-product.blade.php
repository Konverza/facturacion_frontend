<!-- Modal selected product -->
<div id="selected-product" tabindex="-1" aria-hidden="true"
    class="fixed left-0 right-0 top-0 z-[100] hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
    <div class="relative m-4 mb-8 max-h-full w-full max-w-[850px]">
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
                        class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                        data-target="#selected-product">
                        <x-icon icon="x" class="h-5 w-5" />
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4">
                    <livewire:business.tables.dte-product :dte="$dte" :number="$number" />                    
                    <div class="mt-4 hidden" id="container-data-product">
                        <form action="{{ Route('business.dte.product.store') }}" method="POST" id="form-add-product">
                            @csrf
                            <input type="hidden" name="product_id" id="product_id">
                            <div class="flex flex-col gap-4">
                                <x-input type="text" label="Producto" name="product" readonly
                                    id="product_description" />
                                <x-input type="number" name="precio" readonly id="product_price" label="Precio"
                                    icon="currency-dollar" placeholder="0.00" step="0.01" min="0" />
                            </div>
                            <div class="mt-4 hidden" id="price-variant-container">
                                <label for="price_variant_id" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Variante de precio
                                </label>
                                <select name="price_variant_id" id="price_variant_id"
                                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-600 focus:ring-primary-600 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500">
                                    <option value="">Precio base</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" id="price-variant-help">
                                    Si no se selecciona variante, se utilizará el precio base.
                                </p>
                            </div>
                            <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                                <div class="flex-1">
                                    <x-input type="number" label="Cantidad" name="cantidad" id="count" required
                                        label="Cantidad" placeholder="#" min="1" />
                                </div>
                                @if ($number !== '11' && $number !== '14')
                                    <div class="flex-1">
                                        <x-select label="Tipo de venta" name="tipo" id="type-sale" required
                                            :options="[
                                                'Gravada' => ' Gravada',
                                                'Exenta ' => 'Exenta',
                                                'No sujeta' => 'No sujeta',
                                            ]" value="Gravada" selected="Gravada" :search="false" />
                                    </div>
                                @endif
                                @if ($number === '11' || $number === '14')
                                    <x-input type="hidden" name="tipo" value="Gravada" />
                                @endif
                                <div class="flex-1">
                                    <x-input type="number" name="total" id="total_item" placeholder="0.00"
                                        label="Total" icon="currency-dollar" min="0" required step="0.01"
                                        readonly />
                                </div>
                            </div>
                            <div class="mt-4">
                                <x-input type="number" name="descuento" id="descuento_total" label="Descuento"
                                    icon="currency-dollar" placeholder=0.00 step="0.01" min="0" />
                            </div>
                            @if ($number !== '11' && $number !== '14')
                                <div class="select-documento-relacionado mt-4">
                                    <x-select label="Documento relacionado" name="documento_relacionado" id="document"
                                        :required="isset($dte['documentos_relacionados']) &&
                                            count($dte['documentos_relacionados']) > 0" :options="isset($dte['documentos_relacionados'])
                                            ? collect($dte['documentos_relacionados'])->mapWithKeys(function ($item) {
                                                return [
                                                    $item['numero_documento'] =>
                                                        $item['tipo_documento'] . ' - ' . $item['numero_documento'],
                                                ];
                                            })
                                            : []" value="{{ old('documento') }}"
                                        selected="{{ old('documento') }}" :search="false" />
                                </div>
                            @endif
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
                        data-target="#selected-product" class="hide-modal" />
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Modal selected product -->
