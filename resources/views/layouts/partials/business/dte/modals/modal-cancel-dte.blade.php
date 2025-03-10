 <div id="cancel-dte" tabindex="-1" aria-hidden="true"
     class="deleteModal fixed inset-0 left-0 right-0 top-0 z-[100] hidden h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50">
     <div class="relative flex h-full w-full max-w-md items-center justify-center p-4 md:h-auto">
         <!-- Modal content -->
         <div
             class="motion-preset-expand relative w-full rounded-lg bg-white text-center shadow motion-duration-300 dark:bg-gray-900">
             <div class="p-4">
                 <button type="button"
                     class="hide-modal absolute right-2.5 top-2.5 ml-auto inline-flex items-center rounded-lg bg-transparent p-1.5 text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-800 dark:hover:text-white"
                     data-target="#cancel-dte">
                     <x-icon icon="x" class="h-5 w-5" />
                     <span class="sr-only">Close modal</span>
                 </button>
                 <span
                     class="mx-auto mb-4 flex w-max items-center justify-center rounded-full bg-red-100 p-4 text-red-500 dark:bg-red-900/30">
                     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" class="size-12"
                         viewBox="0 0 24 24" fill="currentColor"
                         class="icon icon-tabler icons-tabler-filled icon-tabler-alert-triangle">
                         <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                         <path
                             d="M12 1.67c.955 0 1.845 .467 2.39 1.247l.105 .16l8.114 13.548a2.914 2.914 0 0 1 -2.307 4.363l-.195 .008h-16.225a2.914 2.914 0 0 1 -2.582 -4.2l.099 -.185l8.11 -13.538a2.914 2.914 0 0 1 2.491 -1.403zm.01 13.33l-.127 .007a1 1 0 0 0 0 1.986l.117 .007l.127 -.007a1 1 0 0 0 0 -1.986l-.117 -.007zm-.01 -7a1 1 0 0 0 -.993 .883l-.007 .117v4l.007 .117a1 1 0 0 0 1.986 0l.007 -.117v-4l-.007 -.117a1 1 0 0 0 -.993 -.883z" />
                     </svg>
                 </span>
                 <p class="mb-4 text-gray-500 dark:text-gray-300">
                     ¿Estás seguro de cancelar la generación de este documento?
                 </p>
                 <p class="text-sm text-gray-500 dark:text-gray-400">
                     Perderás toda la información ingresada hasta el momento.
                 </p>
             </div>
             <div class="flex items-center justify-center space-x-4 py-4">
                 <x-button type="button" data-target="#cancel-dte"  text="No, continuar"
                     icon="x" typeButton="secondary" class="hide-modal" />
                 <x-button type="a" href="{{ Route('business.dte.cancel') }}" class="confirmDelete"
                     text="Sí, cancelar" icon="check" typeButton="danger" />
             </div>
         </div>
     </div>
 </div>
