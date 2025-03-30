<div class="mt-4 flex flex-col items-start justify-between gap-4 px-4 sm:flex-row sm:items-center">
    <div class="flex flex-col items-start gap-4 sm:flex-row sm:items-center">
        <span class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
            <x-input type="date" icon="calendar" label="Fecha DTE" name="fecEmi" id="date-dte" class="m-0 bg-transparent p-0 text-sm"
                value="{{ $currentDate }}" required/>
        </span>
        <div class="flex items-center gap-1 text-gray-600 dark:text-gray-400">
            <x-input type="time" icon="clock" label="Hora DTE" name="horEmi" id="time-in-real-time" step="1BB" class="m-0 bg-transparent p-0 text-sm"
                required/>
        </div>
        <div class="flex items-end gap-1 text-gray-600 dark:text-gray-400">
            <x-input type="checkbox" label="Emitir DTE con otra fecha" id="dte-otra-fecha"/>
        </div>
    </div>
    <x-button type="button" typeButton="danger" text="Cancelar documento" icon="cancel"
        class="show-modal w-full sm:w-auto" data-target="#cancel-dte" />
</div>
