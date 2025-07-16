<div class="mt-4 pb-4">
    <!-- Barra de búsqueda rápida (opcional) -->
    <div class="mb-4 flex w-full flex-col gap-4 sm:flex-row">
        <div class="flex-[6] relative">
            <x-input type="text" class="w-full" icon="search" wire:model.live.debounce.500ms="search"
                placeholder="Busqueda rápida (documento o nombre)..." />
            <div wire:loading wire:target="search,searchNombre,searchNumDocumento" class="absolute right-3 top-3">
                <svg class="h-5 w-5 animate-spin text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="flex-1">
            <x-button data-id="" typeButton="success" data-target="#modal-import"
                class="show-modal btn-remove-stock flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900"
                text="Importar Clientes" icon="cloud-upload" />
        </div>
        <div class="flex-1">
            <x-button wire:click="exportToExcel" typeButton="secondary" icon="download" text="Exportar a Excel"
                class="w-full" wire:loading.attr="disabled" />
            <div wire:loading wire:target="exportToExcel" class="mt-2 text-sm text-gray-500">
                Generando archivo Excel, por favor espere...
            </div>
        </div>
        <div class="flex-1">
            <x-button type="a" href="{{ Route('business.customers.create') }}" typeButton="primary"
                text="Nuevo cliente" icon="cube-plus" class="w-full" />
        </div>
    </div>

    <!-- Filtros de búsqueda avanzada -->
    <div class="mb-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white"><x-icon icon="filter"
                    class="inline mr-1" />Filtros de búsqueda</h3>
            <button wire:click="clearFilters" class="text-sm text-blue-500 hover:text-blue-700 dark:text-blue-400">
                Limpiar filtros
            </button>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
            <!-- Búsqueda por código -->
            <div>
                <x-input type="text" wire:model.live.debounce.500ms="searchNumDocumento" label="Buscar por documento"
                    placeholder="Documento del cliente" />
            </div>

            <!-- Búsqueda por nombre -->
            <div>
                <x-input type="text" wire:model.live.debounce.500ms="searchNombre" label="Buscar por nombre"
                    placeholder="Nombre del cliente" />
            </div>
            <!-- Búsqueda exacta -->
            <div class="flex items-center">
                <x-input type="checkbox" wire:model.live="exactSearch" label="Búsqueda exacta" />
            </div>
        </div>
    </div>

    <!-- Resumen de filtros aplicados -->
    @if ($search || $searchNombre || $searchNumDocumento)
        <div class="mb-4 flex flex-wrap items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
            <span class="font-medium">
                Filtros aplicados:
            </span>
            @if ($search)
                <span
                    class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                    Búsqueda rápida: "{{ $search }}"
                    <button wire:click="$set('search', '')"
                        class="ml-1.5 inline-flex h-4 w-4 items-center justify-center rounded-full text-blue-400 hover:bg-blue-200 hover:text-blue-500 dark:hover:bg-blue-800">
                        &times;
                    </button>
                </span>
            @endif

            @if ($searchNombre)
                <span
                    class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                    Nombre: "{{ $searchNombre }}" {{ $exactSearch ? '(exacto)' : '' }}
                    <button wire:click="$set('searchCode', '')"
                        class="ml-1.5 inline-flex h-4 w-4 items-center justify-center rounded-full text-blue-400 hover:bg-blue-200 hover:text-blue-500 dark:hover:bg-blue-800">
                        &times;
                    </button>
                </span>
            @endif

            @if ($searchNumDocumento)
                <span
                    class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                    Documento: "{{ $searchNumDocumento }}" {{ $exactSearch ? '(exacto)' : '' }}
                    <button wire:click="$set('searchName', '')"
                        class="ml-1.5 inline-flex h-4 w-4 items-center justify-center rounded-full text-blue-400 hover:bg-blue-200 hover:text-blue-500 dark:hover:bg-blue-800">
                        &times;
                    </button>
                </span>
            @endif
        </div>
    @endif
    <x-table>
        <x-slot name="thead">
            <x-tr>
                <x-th>#</x-th>
                <x-th wire:click="sortBy('numDocumento')" class="cursor-pointer">
                    Identificación
                    @if ($sortField === 'numDocumento')
                        @if ($sortDirection === 'asc')
                            <x-icon icon="line-up" class="inline w-4 h-4" />
                        @else
                            <x-icon icon="line-down" class="inline w-4 h-4" />
                        @endif
                    @endif
                </x-th>
                <x-th wire:click="sortBy('nombre')" class="cursor-pointer">
                    Nombre
                    @if ($sortField === 'nombre')
                        @if ($sortDirection === 'asc')
                            <x-icon icon="line-up" class="inline w-4 h-4" />
                        @else
                            <x-icon icon="line-down" class="inline w-4 h-4" />
                        @endif
                    @endif
                </x-th>
                <x-th :last="true">Acciones</x-th>
            </x-tr>
        </x-slot>

        <x-slot name="tbody">
            @forelse ($customers as $customer)
                <x-tr :last="$loop->last">
                    <x-td>{{ $loop->iteration + ($customers->firstItem() - 1) }}</x-td>
                    <x-td>
                        {{ $document_types[$customer->tipoDocumento] }}: {{ $customer->numDocumento }}<br>
                        @if ($customer->nrc)
                            <span class="text-xs">NRC: {{ $customer->nrc }}</span>
                        @endif
                    </x-td>
                    <x-td>
                        {{ $customer->nombre }}<br>
                        @if ($customer->nombreComercial)
                            <span class="text-xs">({{ $customer->nombreComercial }})</span>
                        @endif
                    </x-td>
                    <x-td :last="true">
                        <div class="relative">
                            <x-button type="button" icon="arrow-down" typeButton="primary" text="Acciones"
                                class="show-options" data-target="#options-customers-{{ $customer->id }}"
                                size="small" />
                            <div class="options absolute right-0 top-0 z-10 mt-1 hidden w-max rounded-lg border border-gray-300 bg-white p-1 dark:border-gray-800 dark:bg-gray-950"
                                id="options-customers-{{ $customer->id }}">
                                <ul class="flex flex-col text-xs">
                                    <li>
                                        <a href="{{ Route('business.customers.edit', $customer->id) }}"
                                            class="flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                            <x-icon icon="pencil" class="h-4 w-4" />
                                            Editar
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ Route('business.customers.destroy', $customer->id) }}"
                                            method="POST" id="form-delete-customer-{{ $customer->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" data-modal-target="deleteModal"
                                                data-modal-toggle="deleteModal"
                                                data-form="form-delete-customer-{{ $customer->id }}"
                                                class="buttonDelete flex w-full items-center gap-1 rounded-lg px-2 py-2 text-red-500 hover:bg-red-100 dark:text-red-500 dark:hover:bg-red-950/30">
                                                <x-icon icon="trash" class="h-4 w-4" />
                                                Eliminar
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </x-td>
                </x-tr>
            @empty
                <x-tr :last="true">
                    <x-td :colspan="7" class="text-center text-gray-500">
                        @if ($searchNombre)
                            No se encontraron clientes con el nombre "{{ $searchNombre }}"
                            {{ $exactSearch ? '(exacto)' : '' }}.
                        @elseif($searchNumDocumento)
                            No se encontraron clientes con el documento "{{ $searchNumDocumento }}"
                            {{ $exactSearch ? '(exacto)' : '' }}.
                        @else
                            No hay clientes registrados.
                        @endif
                    </x-td>
                </x-tr>
            @endforelse
        </x-slot>
    </x-table>
    @if (!$customers->isEmpty())
        <div class="mt-4">
            {{ $customers->links() }}
        </div>
    @endif
</div>
@push('scripts')
    <script>
        $("#stockState").on("Changed", function() {
            let selectedValue = $(this).val();
            @this.set('stockState', selectedValue);
        });
    </script>
@endpush
