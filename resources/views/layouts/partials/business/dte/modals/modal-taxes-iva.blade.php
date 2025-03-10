<!-- Modal add taxe iva -->
<div id="taxes-iva" tabindex="-1" aria-hidden="true"
    class="fixed left-0 right-0 top-0 z-[100] hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
    <div class="relative max-h-full w-full max-w-lg p-4">
        <!-- Modal content -->
        <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
            <div class="flex flex-col">
                <!-- Modal header -->
                <form action="{{ Route('business.dte.product.taxes-iva') }}" method="POST">
                    @csrf
                    <div
                        class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Item DTE
                        </h3>
                        <button type="button"
                            class="ms-auto hide-modal inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                            data-target="#taxes-iva">
                            <x-icon icon="x" class="h-5 w-5" />
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="flex flex-col gap-4 p-4">
                        <span class="flex items-center gap-1 text-base font-semibold text-gray-500 dark:text-gray-300">
                            <x-icon icon="info-circle" class="size-5" />
                            Impuestos / Tasas con afección al IVA
                        </span>
                        <x-select label="Tipo de venta" id="description" name="descripcion" :options="[
                            'Impuesto especial al combustible (0%, 0.5%, 1%)' =>
                                'Impuesto especial al combustible (0%, 0.5%, 1%)',
                            'Impuesto industria de cemento' => 'Impuesto industria de cemento',
                            'Impuesto especial a la primera matrícula' =>
                                'Impuesto especial a la primera matrícula',
                            'Otros impuestos casos especiales' => 'Otros impuestos casos especiales',
                            'Otras tasas casos especiales' => 'Otras tasas casos especiales',
                            'Impuesto ad-valorem, armas de fuego, municiones, explosivos y artículos similares' =>
                                'Impuesto ad-valorem, armas de fuego, municiones, explosivos y artículos similares',
                        ]" :search="false" />
                        <div class="flex items-center gap-4">
                            <div class="flex-1">
                                <x-input type="number" name="monto" label="Monto" icon="currency-dollar"
                                    placeholder="0.00" required step="0.01" />
                            </div>
                            <div class="flex-1">
                                <x-select label="Tipo de venta" name="tipo" id="tipo_item" :options="[
                                    'Gravada' => 'Gravada',
                                    'Exenta' => 'Exenta',
                                    'No sujeta' => 'No sujeta',
                                ]" :search="false" />
                            </div>
                        </div>
                    </div>
                    <!-- Modal footer -->
                    <div
                        class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                        <x-button type="button" class="hide-modal" text="Cancelar" icon="x" typeButton="secondary"
                            data-target="#taxes-iva" />
                        <x-button type="button" class="submit-form" text="Agregar item" icon="save" typeButton="primary" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End Modal add taxe iva -->
