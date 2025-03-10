<!-- Modal add discount -->
<div id="add-discount" tabindex="-1" aria-hidden="true"
    class="fixed left-0 right-0 top-0 z-[100] hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
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
                            class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                            data-target="#add-discount">
                            <x-icon icon="x" class="h-5 w-5" />
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="flex flex-col gap-4 p-4" id="container-total-discount">
                        @if ($number !== '14')
                            <x-input type="number" label="Descuento a ventas gravadas" icon="percentage"
                                placeholder="0.00" name="descuento_venta_gravadas" id="descuento_venta_gravada"
                                value="{{ isset($dte['percentaje_descuento_venta_gravada']) ? $dte['percentaje_descuento_venta_gravada'] : 0 }}"
                                min="0" max="100" />
                            <x-input type="number" label="Descuento a ventas exentas" icon="percentage"
                                placeholder="0.00" name="descuento_venta_exentas" id="descuento_venta_exentas"
                                max="100" min="0"
                                value="{{ isset($dte['percentaje_descuento_venta_exenta']) ? $dte['percentaje_descuento_venta_exenta'] : 0 }}" />
                            <x-input type="number" label="Descuento a ventas no sujetas" icon="percentage"
                                placeholder="0.00" name="descuento_venta_no_sujetas" id="descuento_venta_no_sujeta"
                                value="{{ isset($dte['percentaje_descuento_venta_no_sujeta']) ? $dte['percentaje_descuento_venta_no_sujeta'] : 0 }}"
                                max="100" min="0" />
                        @else
                            <x-input type="number" label="Descuento" icon="percentage" placeholder="0.00"
                                name="descuento_venta_gravadas" id="descuento_venta_gravada"
                                value="{{ isset($dte['percentaje_descuento_venta_gravada']) ? $dte['percentaje_descuento_venta_gravada'] : 0 }}"
                                min="0" max="100" />
                        @endif
                    </div>
                    <!-- Modal footer -->
                    <div
                        class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                        <x-button type="button" class="hide-modal" text="Cancelar" icon="x"
                            typeButton="secondary" data-target="#add-discount" />
                        <x-button type="button" class="submit-form" text="Aplicar descuento" icon="save"
                            typeButton="primary" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End Modal add discount -->
