<!-- Sección recepción / entrega de documento -->
<div class="mt-6 border-t border-gray-300 px-4 pt-4 dark:border-gray-800">
    <div class="flex items-center gap-4">
        <h2 class="flex items-center gap-1 text-xl font-semibold text-primary-500 dark:text-primary-300">
            <x-icon icon="info-circle" class="size-5" />
            Recepción / entrega de documento
        </h2>
    </div>
    <p class="text-gray-500 dark:text-gray-400">
        Llenar si la operación superior es igual o mayor a {{$monto}}
    </p>
    <div class="mt-4 flex flex-col gap-4 sm:flex-row">
        <div class="flex-1">
            <x-input type="text" name="documento_emitir" label="Número de documento"
                placeholder="Responsable de emitir el documento"
                value="{{ old('documento_emitir', isset($dte['extension']) ? $dte['extension']['docuEntrega'] ?? '' : '') }}" />
        </div>
        <div class="flex-1">
            <x-input type="text" label="Nombre" name="nombre_emitir" placeholder="Responsable de emitir el documento"
                value="{{ old('nombre_emitir', isset($dte['extension']) ? $dte['extension']['nombEntrega'] ?? '' : '') }}" />
        </div>
    </div>
    <div class="mt-4 flex flex-col gap-4 sm:flex-row">
        <div class="flex-1">
            <x-input type="text" name="documento_recibir" label="Número documento"
                placeholder="Responsable de recibir el documento"
                value="{{ old('documento_recibir', isset($dte['extension']) ? $dte['extension']['docuRecibe'] ?? '' : '') }}" />
        </div>
        <div class="flex-1">
            <x-input type="text" label="Nombre" name="nombre_recibir"
                placeholder="Responsable de recibir el documento"
                value="{{ old('nombre_recibir', isset($dte['extension']) ? $dte['extension']['nombRecibe'] ?? '' : '') }}" />
        </div>
    </div>
</div>
<!-- End Sección recepción / entrega de documento -->
