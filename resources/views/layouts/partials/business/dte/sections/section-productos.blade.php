@props(['title' => 'Datos de la factura'])
<!-- Sección datos de la factura -->
<div class="mt-4 border-t border-gray-300 px-4 pt-4 dark:border-gray-800">
    <h2 class="flex items-center gap-1 text-xl font-semibold text-primary-500 dark:text-primary-300">
        <x-icon icon="info-circle" class="size-5" />
        {{ $title }}
    </h2>
    <div class="mt-4 flex flex-col items-center justify-start gap-4 sm:flex-row">
        <div class="relative w-full sm:w-auto">
            <x-button type="button" icon="arrow-down" typeButton="info" text="Agregar detalle"
                class="show-options w-full sm:w-auto" data-target="#options-customers-2" size="normal"
                data-align="right" />
            <div class="options absolute right-0 top-0 z-10 mt-1 hidden w-calc-full-minus-8 sm:w-max rounded-lg border border-gray-300 bg-white p-1 dark:border-gray-800 dark:bg-gray-950"
                id="options-customers-2">
                <ul class="flex flex-col text-sm">
                    <li>
                        <button type="button"
                            class="show-drawer flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900"
                            data-target="#drawer-new-product">
                            <x-icon icon="cube-plus" class="h-4 w-4" />
                            Producto o servicio
                        </button>
                    </li>
                   {{--  <li>
                        <button type="button"
                            class="show-modal flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900"
                            data-target="#unaffected-amounts">
                            <x-icon icon="currency-dollar" class="h-4 w-4" />
                            Monto no afecto
                        </button>
                    </li>
                    <li>
                        <button type="button"
                            class="show-modal flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900"
                            data-target="#taxes-iva">
                            <x-icon icon="moneybag" class="h-4 w-4" />
                            Impuestos / Tasas con afectación de IVA
                        </button>
                    </li> --}}
                </ul>
            </div>
        </div>
        <x-button type="button" text="Seleccionar producto existente" icon="arrow-double-next" typeButton="success"
            data-target="#selected-product" class="show-modal w-full sm:w-auto" />
    </div>
    <div class="mt-4" id="table-products-dte">
        @include("layouts.partials.ajax.business.table-products-dte")
    </div>
</div>
<!-- End Sección datos de la factura -->
