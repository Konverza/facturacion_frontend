@php
    $types = [
        '01' => 'Factura Electrónica',
        '03' => 'Comprobante de crédito fiscal',
        '04' => 'Nota de Remisión',
        '05' => 'Nota de crédito',
        '06' => 'Nota de débito',
        '07' => 'Comprobante de retención',
        '11' => 'Factura de exportación',
        '14' => 'Factura de sujeto excluido'
    ];
    $business_id = Session::get('business') ?? null;
    $business = \App\Models\Business::find($business_id);
    $business_plan = \App\Models\BusinessPlan::where("nit", $business->nit)->first();
    $plan_dtes = json_decode($business_plan->dtes);
    $dte_options = [];
    foreach ($plan_dtes as $dte) {
        $dte_options[$dte] = $types[$dte];
    }

    if(auth()->user()->only_fcf) {
        $dte_options = array_filter($dte_options, function ($key) {
            return $key == '01';
        }, ARRAY_FILTER_USE_KEY);
    }
@endphp
<div id="generate-new-dte" tabindex="-1" aria-hidden="true"
    class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
    <div class="relative max-h-full w-full max-w-lg p-4">
        <!-- Modal content -->
        <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
            <form action="{{ Route('business.dte.create') }}" method="GET" class="flex flex-col">
                <!-- Modal header -->
                <div
                    class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Generar nuevo DTE
                    </h3>
                    <button type="button"
                        class="ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                        data-modal-hide="generate-new-dte">
                        <x-icon icon="x" class="h-5 w-5" />
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4">
                    <x-select :options="$dte_options" name="document_type" id="document_type"
                        label="¿Qué tipo de documento desea emitir?" :search="false" />
                </div>
                <!-- Modal footer -->
                <div
                    class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                    <x-button type="button" text="Cancelar" icon="x" typeButton="secondary"
                        data-modal-hide="generate-new-dte" />
                    <x-button type="submit" text="Generar" icon="file-arrow-right" typeButton="primary" />
                </div>
            </form>
        </div>
    </div>
</div>