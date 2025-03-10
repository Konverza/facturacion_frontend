<!-- Sección observaciones  -->
<div class="mt-4 border-t border-gray-300 px-4 pt-4 dark:border-gray-800">
    <h2 class="flex items-center gap-1 text-xl font-semibold text-primary-500 dark:text-primary-300">
        <x-icon icon="info-circle" class="size-5" />
        Observaciones
    </h2>
    <div class="mt-2">
        <x-input type="textarea" label="Observaciones" name="observaciones" placeholder="Observaciones al documento"
            value="{{ old('observaciones', isset($dte['extension']) ? $dte['extension']['observaciones'] ?? '' : '') }}" />
    </div>
</div>
<!-- End Sección observaciones  -->
