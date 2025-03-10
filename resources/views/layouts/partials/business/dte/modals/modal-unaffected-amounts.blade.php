<!-- Modal add unaffected amounts -->
<div id="unaffected-amounts" tabindex="-1" aria-hidden="true"
    class="fixed left-0 right-0 top-0 z-[100] hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
    <div class="relative max-h-full w-full max-w-md p-4">
        <!-- Modal content -->
        <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
            <div class="flex flex-col">
                <!-- Modal header -->
                <form action="{{ Route('business.dte.product.unaffected-amounts') }}" method="POST">
                    @csrf
                    <div
                        class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Item DTE
                        </h3>
                        <button type="button"
                            class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                            data-target="#unaffected-amounts">
                            <x-icon icon="x" class="h-5 w-5" />
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="flex flex-col gap-4 p-4">
                        <span class="flex items-center gap-1 text-base font-semibold text-gray-500 dark:text-gray-300">
                            <x-icon icon="info-circle" class="size-5" />
                            Adición detalle de DTE
                        </span>
                        <x-input type="textarea" name="descripcion" label="Descripción"
                            placeholder="Descripción del item" required />
                        <x-input type="number" name="monto" label="Monto" icon="currency-dollar"
                            placeholder="0.00" required step="0.01" />
                    </div>
                    <!-- Modal footer -->
                    <div
                        class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                        <x-button type="button" class="hide-modal" text="Cancelar" icon="x" typeButton="secondary"
                            data-target="#unaffected-amounts" />
                        <x-button type="button" class="submit-form" text="Agregar item" icon="save" typeButton="primary" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End Modal add unaffected amounts -->
