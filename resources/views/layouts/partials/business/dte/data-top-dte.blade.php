 <div class="mt-4 flex flex-col items-start justify-between gap-4 px-4 sm:flex-row sm:items-center">
     <div class="flex flex-col items-start gap-4 sm:flex-row sm:items-center">
         <span class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
             <x-icon icon="calendar" class="size-4" />
             Fecha DTE:
             {{ $currentDate }}
         </span>
         <div class="flex items-center gap-1 text-gray-600 dark:text-gray-400">
             <x-icon icon="clock" class="size-4" />
             <input type="time" id="time-in-real-time"
                 class="m-0 border-none bg-transparent p-0 text-sm focus:border-none focus:outline-none" readonly>
         </div>
     </div>
     <x-button type="button" typeButton="danger" text="Cancelar documento" icon="cancel" class="show-modal w-full sm:w-auto"
         data-target="#cancel-dte"  />
 </div>
