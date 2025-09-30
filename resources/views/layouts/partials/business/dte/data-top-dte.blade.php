<div class="mt-4 flex flex-col items-start justify-between gap-4 px-4 sm:flex-row sm:items-center">
    <div class="flex flex-col items-start gap-4 sm:flex-row sm:items-center">
        <span class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
            <x-input type="date" icon="calendar" label="Fecha DTE" name="fecEmi" id="date-dte"
                class="m-0 bg-transparent p-0 text-sm" value="{{ $currentDate }}" required />
        </span>
        <div class="flex items-center gap-1 text-gray-600 dark:text-gray-400">
            <x-input type="time" icon="clock" label="Hora DTE" name="horEmi" id="time-in-real-time" step="1"
                class="m-0 bg-transparent p-0 text-sm" required />
        </div>
        <div class="flex items-end gap-1 text-gray-600 dark:text-gray-400">
            <x-input type="checkbox" label="Emitir DTE con otra fecha" id="dte-otra-fecha" />
        </div>
    </div>
    <x-button type="button" typeButton="danger" text="Cancelar documento" icon="cancel"
        class="show-modal w-full sm:w-auto" data-target="#cancel-dte" />
</div>
@if (isset($dte['use_template']))
    <input type="hidden" name="use_template" value="1" />
@endif
@if ($business->bulk_emission && in_array($number, ['01', '03']) && !isset($dte['use_template']))
    <div class="mt-4 border-t border-gray-300 px-4 pt-4 dark:border-gray-800">
        <h2 class="flex items-center gap-1 text-xl font-semibold text-primary-500 dark:text-primary-300">
            <x-icon icon="files" class="size-5" />
            Plantilla de DTE
        </h2>
        <div
            class="my-4 rounded-lg border border-dashed border-blue-500 bg-blue-100 p-4 text-blue-500 dark:bg-blue-950/30">
            Puede guardar el DTE como plantilla para enviarlo por lotes o reutilizarlo nuevamente.
        </div>
        <div class="mt-2 flex flex-col gap-2">
            <div>
                <x-input type="checkbox" label="Guardar DTE como plantilla" name="save_as_template"
                    id="save_as_template" :checked="$dte['status'] == 'template'" />
            </div>
            <div id="template_name" class="{{ $dte['status'] == 'template' ? '' : 'hidden' }}">
                <x-input type="text" label="Nombre de la plantilla" name="template_name"
                    placeholder="Nombre de la plantilla" class="w-full" value="{{ $dte['name'] ?? '' }}" />
            </div>
        </div>
    </div>
@endif
