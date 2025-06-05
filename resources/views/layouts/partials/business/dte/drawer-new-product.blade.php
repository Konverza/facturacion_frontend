<!-- Drawer new product  -->
<div id="drawer-new-product"
    class="fixed left-0 top-0 z-[300] h-screen w-full -translate-x-full overflow-y-auto bg-white p-4 transition-transform dark:bg-gray-950 md:w-[650px]"
    tabindex="-1" aria-labelledby="drawer-label">
    <h5 id="drawer-label" class="mb-4 inline-flex items-center text-lg font-semibold text-gray-900 dark:text-white">
        Añadir producto o servicio
    </h5>
    <div class="my-4 rounded-lg border border-dashed border-yellow-500 bg-yellow-100 p-4 text-yellow-500 dark:bg-yellow-950/30">
        <b>Nota: </b> Se ha actualizado el sistema, ahora estos productos <b>NO</b> serán guardados en la base de datos.
    </div>
    <button type="button" data-target="#drawer-new-product" aria-controls="drawer-new-product"
        class="hide-drawer absolute end-2.5 top-2.5 flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white">
        <x-icon icon="x" class="h-5 w-5" />
        <span class="sr-only">Cerrar</span>
    </button>
    <div>
        <form action="{{ Route('business.dte.product.store-new') }}" method="POST">
            @csrf
            <div class="flex flex-col gap-4 sm:flex-row">
                <div class="flex-[2]">
                    <x-select label="Tipo de producto" value="{{ old('tipo_producto') }}" required
                        selected="{{ old('tipo_producto') }}" name="tipo_item" id="product_type" :options="[
                            '1' => 'Bien',
                            '2' => 'Servicio',
                            '3' => 'Bien y servicio',
                        ]"
                        :search="false" required />
                </div>
                <div class="flex-[2]">
                    <x-select id="unidad_medida" required name="unidad_medida" :options="$unidades_medidas"
                        value="{{ old('unidad_medida') }}" selected="{{ old('unidad_medida') }}"
                        label="Unidad de medida" required />
                </div>
            </div>
            <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                <div class="flex-[2]">
                    <x-input type="text" id="descripcion_product" placeholder="Nombre del producto" label="Producto"
                        name="descripcion" required />
                </div>
                @if ($number !== '11' && $number !== '14')
                    <div class="flex-1">
                        <x-select label="Tipo de venta" name="tipo" id="tipo_venta" :options="[
                            'Gravada' => 'Gravada',
                            'Exenta' => 'Exenta',
                            'No sujeta' => 'No sujeta',
                        ]"
                            :search="false" required />
                    </div>
                @else
                    <x-input type="hidden" name="tipo" value="Gravada" />
                @endif
            </div>
            <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                
                <div class="flex-1">
                    <x-input type="number" label="Cantidad" placeholder="#" min="1" name="cantidad"
                        id="count_product" required />
                </div>
                <div class="flex-1">
                    @php
                        if(in_array($number, ["03", "04", "05", "06"])){
                            $label_precio = "Precio (sin IVA)";
                        } elseif ($number == "14"){
                            $label_precio = "Precio (sin Retención de Renta)";
                        } elseif ($number == "11"){
                            $label_precio = "Precio";
                        } else {
                            $label_precio = "Precio (con IVA)";
                        }
                    @endphp
                    <x-input type="number" icon="currency-dollar" id="price" placeholder="0.00" :label="$label_precio"
                        name="precio_unitario" step="0.00000001" min="0.00000001" required />
                </div>
            </div>
            <div class="mt-4 flex gap-4">
                <div class="flex-1">
                    <x-input type="number" name="descuento" id="descuento_product" label="Descuento"
                        icon="currency-dollar" placeholder=0.00 step=0.00000001 min=0.00000001 />
                </div>
                <div class="flex-1">
                    <x-input type="number" name="total" id="total_product" placeholder="0.00" label="Total"
                        icon="currency-dollar" step="0.00000001" min="0.00000001" required />
                </div>
            </div>
            @if ($number !== '11' || $number !== '14')
                <div class="select-documento-relacionado-new mt-4">
                    <x-select label="Documento relacionado" name="documento_relacionado" id="documento_relacionado_new"
                        :required="isset($dte['documentos_relacionados']) && count($dte['documentos_relacionados']) > 0" :options="isset($dte['documentos_relacionados'])
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
            @if ($number !== '11' && $number !== '14')
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
            @endif
            <div class="mt-4 flex flex-col gap-2">
                <input type="hidden" name="tributos[]" value="20" />
                @if ($number !== '11' && $number !== '14')
                    <x-input name="tributos[]" type="toggle" label="Impuesto al valor agregado (13%)" checked
                        disabled value="20" />
                    <x-input name="tributos[]" type="toggle" label="Turismo: por alojamiento (5%)"
                        data-list="#turismo-list" class="tax-toggle" value="59" />
                    <x-input name="tributos[]" type="toggle" value="71"
                        label="Turismo: salida del país por vía áerea" id="turismo-salida-pais" class="tax-toggle"
                        data-list="#turismo-salida-pais-list" />
                    <x-input name="tributos[]" type="toggle" value="D1"
                        label="FOVIAL ($0.20 por galón de combustible)" id="fovial" class="tax-toggle"
                        data-list="#fovial-list" />
                    <x-input name="tributos[]" type="toggle" value="C8"
                        label="COTRANS ($0.10 por galón de combustible)" id="cotrans" class="tax-toggle"
                        data-list="#cotrans-list" />
                    <x-input name="tributos[]" type="toggle" value="C5"
                        label="Impuesto ad-valorem por diferencial de precio de bebidas alcohólicas (8%)"
                        data-list="#add-valorem-bebidas-alcoholicas-list" class="tax-toggle" />
                    <x-input name="tributos[]" type="toggle" value="C6"
                        label="Impuesto ad-valorem por diferencial de precio al tabaco cigarrillos (39%)"
                        data-list="#add-valorem-tabaco-cigarrillos-list" class="tax-toggle" />
                    <x-input name="tributos[]" type="toggle" value="C7"
                        label="Impuesto ad-valorem por diferencial de precio al tabaco cigarros (100%)"
                        data-list="#add-valorem-tabaco-cigarros-list" class="tax-toggle" />
                @endif
            </div>
            <div class="mt-4 flex items-center justify-center">
                <x-button type="button" class="submit-form" typeButton="primary" text="Añadir producto"
                    icon="save" />
            </div>
        </form>
    </div>
</div>
<!-- End Drawer new product  -->
