  <div id="{{ $modalId }}" tabindex="-1" aria-hidden="true"
      class="deleteModal fixed inset-0 left-0 right-0 top-0 z-[100] hidden h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50">
      <div class="relative flex h-full w-full max-w-md items-center justify-center p-4 md:h-auto">
          <!-- Modal content -->
          <div
              class="motion-preset-expand relative w-full rounded-lg bg-white text-center shadow motion-duration-300 dark:bg-gray-950">
              <div class="p-4">
                  <button type="button"
                      class="closeModal absolute right-2.5 top-2.5 ml-auto inline-flex items-center rounded-lg bg-transparent p-1.5 text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                      data-modal-toggle="{{ $modalId }}">
                      <svg aria-hidden="true" class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"
                          xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd"
                              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                              clip-rule="evenodd"></path>
                      </svg>
                      <span class="sr-only">Close modal</span>
                  </button>
                  <span
                      class="mx-auto mb-4 flex w-max items-center justify-center rounded-full bg-red-100 p-3 text-red-500 dark:bg-red-900/30">
                      <svg class="mx-auto size-11 text-current" aria-hidden="true" fill="currentColor"
                          viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd"
                              d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                              clip-rule="evenodd"></path>
                      </svg>
                  </span>
                  <p class="mb-4 text-gray-500 dark:text-gray-300">{{ $title }}</p>
                  <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">{{ $message }}</p>
              </div>
              <div class="flex items-center justify-center space-x-4 py-4">
                  <x-button type="button" data-modal-toggle="{{ $modalId }}" class="closeModal"
                      text="No, cancelar" icon="x" typeButton="secondary" />
                  <x-button type="button" class="confirmDelete" text="Sí, eliminar" icon="trash"
                      typeButton="danger" />
              </div>
          </div>
      </div>
  </div>
