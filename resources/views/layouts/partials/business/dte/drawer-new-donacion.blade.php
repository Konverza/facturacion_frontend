<!-- Drawer new product  -->
<div id="drawer-new-product"
    class="fixed left-0 top-0 z-[300] h-screen w-full -translate-x-full overflow-y-auto bg-white p-4 transition-transform dark:bg-gray-950 md:w-[650px]"
    tabindex="-1" aria-labelledby="drawer-label">
    <h5 id="drawer-label" class="mb-4 inline-flex items-center text-lg font-semibold text-gray-900 dark:text-white">
        Añadir donación
    </h5>
    <button type="button" data-target="#drawer-new-product" aria-controls="drawer-new-product"
        class="hide-drawer absolute end-2.5 top-2.5 flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white">
        <x-icon icon="x" class="h-5 w-5" />
        <span class="sr-only">Cerrar</span>
    </button>
    <div>
        <form action="{{ Route('business.dte.donation.store') }}" method="POST">
            @csrf
            <div class="flex flex-col gap-4 sm:flex-row">
                <div class="flex-[2]">
                    <x-select label="Tipo de donación" value="{{ old('tipo_donacion') }}" required
                        selected="{{ old('tipo_donacion') }}" name="tipo_donacion" id="tipo_donacion" :options="[
                            '1' => 'Efectivo',
                            '2' => 'Bien',
                            '3' => 'Servicio',
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
            </div>
            <div class="mt-4 flex flex-col gap-4 sm:flex-row">

                <div class="flex-1">
                    <x-input type="number" label="Cantidad" placeholder="#" min="1" name="cantidad"
                        id="count_product" required />
                </div>
                <div class="flex-1">
                    <x-input type="number" icon="currency-dollar" id="valor_unitario" placeholder="0.00" label="Valor Unitario"
                        name="valor_unitario" step="0.00000001" min="0.00000001" required />
                </div>
            </div>
            <div class="mt-4 flex gap-4">
                <div class="flex-1">
                    <x-input type="number" icon="currency-dollar" id="depreciacion" placeholder="0.00" label="Depreciación"
                        name="depreciacion" step="0.00000001" min="0.00000001" required />
                </div>
                <div class="flex-1">
                    <x-input type="number" name="valor_donado" id="valor_donado" placeholder="0.00" label="Valor Donado"
                        icon="currency-dollar" step="0.00000001" min="0.00000001" required readonly />
                </div>
            </div>
            <div class="mt-4 flex items-center justify-center">
                <x-button type="button" class="submit-form" typeButton="primary" text="Añadir donación"
                    icon="save" />
            </div>
        </form>
    </div>
</div>
<!-- End Drawer new product  -->
