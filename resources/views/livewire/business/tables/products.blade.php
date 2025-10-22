<div class="mt-4 pb-4">
    <!-- Selector de sucursal (si aplica) -->
    @if ($canSelectBranch && !empty($availableSucursales))
        <div class="mb-4">
            <label for="sucursal-selector" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                <x-icon icon="building-store" class="inline w-4 h-4" /> Seleccionar Sucursal
            </label>
            <select wire:model.live="selectedSucursalId" id="sucursal-selector"
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="">Seleccione una sucursal</option>
                <option value="all">📊 Ver todas las sucursales (Inventario Global)</option>
                <option disabled>──────────</option>
                @foreach ($availableSucursales as $id => $nombre)
                    <option value="{{ $id }}">{{ $nombre }}</option>
                @endforeach
            </select>
        </div>
    @elseif ($defaultSucursalId)
        <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800" 
             data-sucursal-id="{{ $selectedSucursalId }}" 
             data-sucursal-nombre="{{ $availableSucursales[$defaultSucursalId] ?? 'Sucursal por defecto' }}">
            <p class="text-sm text-blue-700 dark:text-blue-300">
                <x-icon icon="map-pin" class="inline w-4 h-4" /> Mostrando productos de: <strong>{{ $availableSucursales[$defaultSucursalId] ?? 'Sucursal por defecto' }}</strong>
            </p>
        </div>
    @endif

    <!-- Barra de búsqueda rápida (opcional) -->
    <div class="mb-4 flex w-full flex-col gap-4 sm:flex-row">
        <div class="flex-[6] relative">
            <x-input type="text" class="w-full" icon="search" wire:model.live.debounce.500ms="search"
                placeholder="Busqueda rápida (código o nombre)..." />
            <div wire:loading wire:target="search,searchName,searchCode" class="absolute right-3 top-3">
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
                text="Importar Productos" icon="cloud-upload" />
        </div>
        <div class="flex-1">
            <x-button wire:click="exportToExcel" typeButton="secondary" icon="download" text="Exportar a Excel"
                class="w-full" wire:loading.attr="disabled" />
            <div wire:loading wire:target="exportToExcel" class="mt-2 text-sm text-gray-500">
                Generando archivo Excel, por favor espere...
            </div>
        </div>
        <div class="flex-1">
            <x-button type="a" href="{{ Route('business.products.create') }}" typeButton="primary"
                text="Nuevo producto" icon="cube-plus" class="w-full" />
        </div>
    </div>

    <!-- Filtros de búsqueda avanzada -->
    <div class="mb-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white"><x-icon icon="filter" class="inline mr-1" />Filtros de búsqueda</h3>
            <button wire:click="clearFilters" class="text-sm text-blue-500 hover:text-blue-700 dark:text-blue-400">
                Limpiar filtros
            </button>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
            <!-- Búsqueda por código -->
            <div>
                <x-input type="text" wire:model.live.debounce.500ms="searchCode" label="Buscar por código"
                    placeholder="Código del producto" />
            </div>

            <!-- Búsqueda por nombre -->
            <div>
                <x-input type="text" wire:model.live.debounce.500ms="searchName" label="Buscar por nombre"
                    placeholder="Nombre del producto" />
            </div>
            <!-- Filtro por estado stock -->
            <div>
                <x-select wire:model.live.debounce.500ms="stockState" :options="$options" label="Estado del stock"
                    :search="false" id="stockState" name="stockState" value="{{ $stockState }}"
                    selected="{{ $stockState }}" />
            </div>

            <!-- Búsqueda exacta -->
            <div class="flex items-center">
                <x-input type="checkbox" wire:model.live="exactSearch" label="Búsqueda exacta" />
            </div>
        </div>
    </div>

    <!-- Resumen de filtros aplicados -->
    @if ($search || $searchName || $searchCode || $stockState)
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

            @if ($searchCode)
                <span
                    class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                    Código: "{{ $searchCode }}" {{ $exactSearch ? '(exacto)' : '' }}
                    <button wire:click="$set('searchCode', '')"
                        class="ml-1.5 inline-flex h-4 w-4 items-center justify-center rounded-full text-blue-400 hover:bg-blue-200 hover:text-blue-500 dark:hover:bg-blue-800">
                        &times;
                    </button>
                </span>
            @endif

            @if ($searchName)
                <span
                    class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                    Nombre: "{{ $searchName }}" {{ $exactSearch ? '(exacto)' : '' }}
                    <button wire:click="$set('searchName', '')"
                        class="ml-1.5 inline-flex h-4 w-4 items-center justify-center rounded-full text-blue-400 hover:bg-blue-200 hover:text-blue-500 dark:hover:bg-blue-800">
                        &times;
                    </button>
                </span>
            @endif

            @if ($stockState)
                <span
                    class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                    Estado del stock: "{{ $options[$stockState] }}"
                    <button wire:click="$set('stockState', '')"
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
                <x-th wire:click="sortBy('codigo')" class="cursor-pointer">
                    Código
                    @if ($sortField === 'codigo')
                        @if ($sortDirection === 'asc')
                            <x-icon icon="line-up" class="inline w-4 h-4" />
                        @else
                            <x-icon icon="line-down" class="inline w-4 h-4" />
                        @endif
                    @endif
                </x-th>
                <x-th wire:click="sortBy('descripcion')" class="cursor-pointer">
                    Descripción
                    @if ($sortField === 'descripcion')
                        @if ($sortDirection === 'asc')
                            <x-icon icon="line-up" class="inline w-4 h-4" />
                        @else
                            <x-icon icon="line-down" class="inline w-4 h-4" />
                        @endif
                    @endif
                </x-th>
                <x-th wire:click="sortBy('precioSinTributos')" class="cursor-pointer">
                    Precios
                    @if ($sortField === 'precioSinTributos')
                        @if ($sortDirection === 'asc')
                            <x-icon icon="line-up" class="inline w-4 h-4" />
                        @else
                            <x-icon icon="line-down" class="inline w-4 h-4" />
                        @endif
                    @endif
                </x-th>
                <x-th wire:click="sortBy('stockActual')" class="cursor-pointer">
                    Stock
                    @if ($sortField === 'stockActual')
                        @if ($sortDirection === 'asc')
                            <x-icon icon="line-up" class="inline w-4 h-4" />
                        @else
                            <x-icon icon="line-down" class="inline w-4 h-4" />
                        @endif
                    @endif
                </x-th>
                <x-th wire:click="sortBy('estado_stock')" class="cursor-pointer">
                    Estado
                    @if ($sortField === 'estado_stock')
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
            @forelse ($products as $product)
                <x-tr :last="$loop->last">
                    <x-td>{{ $loop->iteration + ($products->firstItem() - 1) }}</x-td>
                    <x-td>{{ $product->codigo }}</x-td>
                    <x-td>
                        {{ $product->descripcion }}<br>
                        @if ($product->category)
                            <span class="text-xs text-gray-500">{{ $product->category->name }}</span>
                        @endif
                    </x-td>
                    <x-td>
                        <div class="flex flex-col gap-1">
                            <span>Sin IVA: ${{ $product->precioSinTributos }}</span>
                            <span>Con IVA: ${{ $product->precioUni }}</span>
                            @if ($product->special_price > 0)
                                <span class="text-green-500">Descuento (Sin IVA): ${{ $product->special_price }}</span>
                                <span class="text-green-500">Descuento (Con IVA): ${{ $product->special_price_with_iva }}</span>
                            @endif
                        </div>
                    </x-td>
                    @if ($product->is_global)
                        <x-td>
                            <span class="text-blue-600 dark:text-blue-400 text-xs font-semibold flex items-center gap-1">
                                <x-icon icon="world" class="w-4 h-4" />
                                GLOBAL
                            </span>
                        </x-td>
                        <x-td>
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                Sin control de stock
                            </span>
                        </x-td>
                    @elseif ($product->has_stock)
                        <x-td>
                            @if ($viewAllBranches && isset($product->stockPorSucursales))
                                <!-- Modo: Ver todas las sucursales -->
                                <div class="flex flex-col gap-1 text-xs">
                                    @php
                                        $totalStock = 0;
                                    @endphp
                                    @forelse ($product->stockPorSucursales as $stockInfo)
                                        @php
                                            $totalStock += $stockInfo['stock'];
                                        @endphp
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600 dark:text-gray-400">{{ $stockInfo['sucursal_nombre'] }}:</span>
                                            <span class="font-medium ml-2">{{ $stockInfo['stock'] }}</span>
                                        </div>
                                    @empty
                                        <span class="text-gray-500">Sin stock en sucursales</span>
                                    @endforelse
                                    @if ($product->stockPorSucursales->count() > 0)
                                        <div class="flex justify-between items-center pt-1 mt-1 border-t border-gray-300 dark:border-gray-600">
                                            <span class="text-gray-900 dark:text-gray-100 font-semibold">Total:</span>
                                            <span class="font-bold ml-2 text-gray-900 dark:text-gray-100">{{ $totalStock }}</span>
                                        </div>
                                    @endif
                                </div>
                            @elseif ($selectedSucursalId && !$viewAllBranches)
                                <span class="font-medium">{{ $product->stockPorSucursal ?? 0 }}</span>
                                <span class="text-xs text-gray-500 block">en sucursal</span>
                            @else
                                <span class="font-medium">{{ $product->stockActual ?? 0 }}</span>
                                <span class="text-xs text-gray-500 block">total</span>
                            @endif
                        </x-td>
                        <x-td>
                            @if ($viewAllBranches && isset($product->stockPorSucursales))
                                <!-- Modo: Ver todas las sucursales - mostrar estados múltiples -->
                                <div class="flex flex-col gap-1">
                                    @foreach ($product->stockPorSucursales as $stockInfo)
                                        @if ($stockInfo['estado'] === 'disponible')
                                            <span class="flex w-max items-center gap-1 text-nowrap rounded-lg bg-green-200 px-2 py-1 text-xs font-bold text-green-800 dark:bg-green-900/50 dark:text-green-300">
                                                <x-icon icon="circle-check" class="size-3" />
                                                {{ $stockInfo['sucursal_nombre'] }}
                                            </span>
                                        @elseif($stockInfo['estado'] === 'por_agotarse')
                                            <span class="flex items-center gap-1 text-nowrap rounded-lg bg-yellow-200 px-2 py-1 text-xs font-bold text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300 w-max">
                                                <x-icon icon="alert-circle" class="size-3" />
                                                {{ $stockInfo['sucursal_nombre'] }}
                                            </span>
                                        @else
                                            <span class="flex w-max items-center gap-1 text-nowrap rounded-lg bg-red-200 px-2 py-1 text-xs font-bold text-red-800 dark:bg-red-900/50 dark:text-red-300">
                                                <x-icon icon="circle-x" class="size-3" />
                                                {{ $stockInfo['sucursal_nombre'] }}
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                @php
                                    $estado = $selectedSucursalId && !$viewAllBranches ? $product->estadoStockSucursal : $product->estado_stock;
                                @endphp
                                @if ($estado === 'disponible')
                                    <span class="flex w-max items-center gap-1 text-nowrap rounded-lg bg-green-200 px-2 py-1 text-xs font-bold text-green-800 dark:bg-green-900/50 dark:text-green-300">
                                        <x-icon icon="circle-check" class="size-4" />
                                        Disponible
                                    </span>
                                @elseif($estado === 'por_agotarse')
                                    <span class="flex items-center gap-1 text-nowrap rounded-lg bg-yellow-200 px-2 py-1 text-xs font-bold text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300 w-max">
                                        <x-icon icon="alert-circle" class="size-4" />
                                        Por agotarse
                                    </span>
                                @else
                                    <span class="flex w-max items-center gap-1 text-nowrap rounded-lg bg-red-200 px-2 py-1 text-xs font-bold text-red-800 dark:bg-red-900/50 dark:text-red-300">
                                        <x-icon icon="circle-x" class="size-4" />
                                        Agotado
                                    </span>
                                @endif
                            @endif
                        </x-td>
                    @else
                        @for ($i = 0; $i < 2; $i++)
                            <x-td><span class="text-xs text-gray-500">N/A</span></x-td>
                        @endfor
                    @endif
                    <x-td :last="true">
                        <div class="relative">
                            <x-button type="button" icon="arrow-down" typeButton="primary" text="Acciones"
                                class="show-options" data-target="#options-products-{{ $product->id }}"
                                size="small" />
                            <div class="options absolute right-0 top-0 z-10 mt-1 hidden w-max rounded-lg border border-gray-300 bg-white p-1 dark:border-gray-800 dark:bg-gray-950"
                                id="options-products-{{ $product->id }}">
                                <ul class="flex flex-col text-xs">
                                    <li>
                                        <a href="{{ Route('business.products.edit', $product->id) }}"
                                            class="flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                            <x-icon icon="pencil" class="h-4 w-4" />
                                            Editar
                                        </a>
                                    </li>
                                    @if ($product->has_stock && !$product->is_global && $selectedSucursalId && !$viewAllBranches)
                                        <li>
                                            <button data-id="{{ $product->id }}" data-target="#modal-add-stock"
                                                class="show-modal btn-add-stock flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                <x-icon icon="plus" class="h-4 w-4" />
                                                Agregar stock
                                            </button>
                                        </li>
                                        <li>
                                            <button data-id="{{ $product->id }}" data-target="#modal-remove-stock"
                                                class="show-modal btn-remove-stock flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                <x-icon icon="minus" class="h-4 w-4" />
                                                Disminuir stock
                                            </button>
                                        </li>
                                    @endif
                                    @if ($product->has_stock && !$product->is_global && $canSelectBranch && $selectedSucursalId && !$viewAllBranches)
                                        <li>
                                            <button data-id="{{ $product->id }}" data-target="#modal-transfer-stock"
                                                class="show-modal btn-transfer-stock flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                <x-icon icon="transfer" class="h-4 w-4" />
                                                Trasladar stock
                                            </button>
                                        </li>
                                    @endif
                                    <li>
                                        <form action="{{ Route('business.products.destroy', $product->id) }}"
                                            method="POST" id="form-delete-product-{{ $product->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                data-form="form-delete-product-{{ $product->id }}"
                                                data-modal-toggle="deleteModal" data-modal-target="deleteModal"
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
                        @if ($searchCode)
                            No se encontraron productos con el código "{{ $searchCode }}"
                            {{ $exactSearch ? '(exacto)' : '' }}.
                        @elseif($searchName)
                            No se encontraron productos con el nombre "{{ $searchName }}"
                            {{ $exactSearch ? '(exacto)' : '' }}.
                        @else
                            No hay productos registrados.
                        @endif
                    </x-td>
                </x-tr>
            @endforelse
        </x-slot>
    </x-table>
    @if (!$products->isEmpty())
        <div class="mt-4">
            {{ $products->links() }}
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
