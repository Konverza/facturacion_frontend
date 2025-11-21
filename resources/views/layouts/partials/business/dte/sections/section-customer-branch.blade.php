<!-- Sección para seleccionar sucursal del cliente -->
<div class="mt-4 hidden" id="customer-branches-section">
    <div class="rounded-lg border border-primary-300 bg-primary-50 p-4 dark:border-primary-800 dark:bg-primary-950/30">
        <h3 class="mb-2 flex items-center gap-2 font-semibold text-primary-700 dark:text-primary-300">
            <x-icon icon="building-store" class="h-5 w-5" />
            Sucursal del Cliente
        </h3>
        <div id="branches-list">
            <select name="customer_branch_id" id="customer_branch_select" 
                class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-950 dark:text-white dark:placeholder-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500">
                <option value="">Seleccione una sucursal</option>
            </select>
        </div>
    </div>
</div>
