<!-- Sección condición de la operación -->
<div class="mt-4 border-t border-gray-300 px-4 pt-4 dark:border-gray-800">
    <h2 class="flex items-center gap-1 text-xl font-semibold text-primary-500 dark:text-primary-300">
        <x-icon icon="info-circle" class="size-5" />
        Condición de la operación
    </h2>
    <div class="mt-2">
        <x-select id="condicion_operacion" name="condicion_operacion" label="Condición de la operación"
            name="condicion_operacion" :options="[
                '1' => 'Contado',
                '2' => 'Crédito',
                '3' => 'Otro',
            ]" :search="false" value="1" selected="1" required  />
    </div>
</div>
<!-- End Sección condición de la operación -->
