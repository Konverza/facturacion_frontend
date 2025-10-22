<div class="mt-4 pb-4">
    <!-- Selector de sucursal (si aplica) -->
    @if ($canSelectBranch && !empty($availableSucursales))
        <div class="mb-4">
            <label for="sucursal-selector" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Seleccionar Sucursal
            </label>
            <select wire:model.live="selectedSucursalId" id="sucursal-selector"
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="">Productos Globales</option>
                @foreach ($availableSucursales as $id => $nombre)
                    <option value="{{ $id }}">{{ $nombre }}</option>
                @endforeach
            </select>
        </div>
    @elseif ($defaultSucursalId)
        <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
            <p class="text-sm text-blue-700 dark:text-blue-300">
                📍 Mostrando productos de: <strong>{{ $availableSucursales[$defaultSucursalId] ?? 'Sucursal por defecto' }}</strong>
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
    </div>
    <x-table>
        <x-slot name="thead">
            <x-tr>
                <x-th class="w-10">#</x-th>
                <x-th>Código</x-th>
                <x-th>Precio</x-th>
                <x-th>Descripción</x-th>
                <x-th>Stock</x-th>
                <x-th>Tipo</x-th>
                <x-th :last="true"></x-th>
            </x-tr>
        </x-slot>
        <x-slot name="tbody" id="table-selected-product">
            @foreach ($products as $product)
                <x-tr>
                    <x-td>
                        {{ $loop->iteration }}
                    </x-td>
                    <x-td>
                        {{ $product->codigo }}
                    </x-td>
                    <x-td>
                        @if ($number !== '01')
                            ${{ $product->precioSinTributos }} <br>
                            @if ($product->special_price > 0)
                                <span class="text-success">Con descuento: ${{ $product->special_price }}</span>
                            @endif
                        @else
                            ${{ $product->precioUni }} <br>
                            @if ($product->special_price_with_iva > 0)
                                <span class="text-success">Con descuento: ${{ $product->special_price_with_iva }}</span>
                            @endif
                        @endif
                    </x-td>
                    <x-td>
                        {{ $product->descripcion }}
                    </x-td>
                    <x-td>
                        @if ($product->is_global)
                            <span class="text-blue-600 dark:text-blue-400 text-xs font-semibold">
                                🌐 GLOBAL
                            </span>
                        @elseif ($product->has_stock)
                            <span class="font-medium">
                                {{ $product->stockPorSucursal ?? 0 }}
                            </span>
                            @if ($product->estadoStockSucursal === 'agotado')
                                <span class="text-red-600 dark:text-red-400 text-xs">(Agotado)</span>
                            @elseif ($product->estadoStockSucursal === 'por_agotarse')
                                <span class="text-yellow-600 dark:text-yellow-400 text-xs">(Bajo)</span>
                            @endif
                        @else
                            <span class="text-gray-500 text-xs">N/A</span>
                        @endif
                    </x-td>
                    <x-td>
                        @if ($product->is_global)
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                Global
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                Sucursal
                            </span>
                        @endif
                    </x-td>
                    <x-td :last="true">
                        <form method="POST" action="{{ Route('business.dte.product.select') }}">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="sucursal_id" value="{{ $selectedSucursalId }}">
                            <x-button type="button" icon="arrow-next" size="small" class="btn-selected-product"
                                typeButton="secondary" text="Seleccionar" />
                        </form>
                    </x-td>
                </x-tr>
            @endforeach
        </x-slot>
    </x-table>
    @if (!$products->isEmpty())
        <div class="mt-4">
            {{ $products->links() }}
        </div>
    @endif
</div>
