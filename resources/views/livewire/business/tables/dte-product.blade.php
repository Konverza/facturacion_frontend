<div class="mt-4 pb-4">
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
                            ${{ $product->precioSinTributos }}
                        @else
                            ${{ $product->precioUni }}
                        @endif
                    </x-td>
                    <x-td>
                        {{ $product->descripcion }}
                    </x-td>
                    <x-td>
                        @if ($product->has_stock)
                            {{ $product->stockActual }}
                        @else
                            N/A
                        @endif
                    </x-td>
                    <x-td :last="true">
                        <form method="POST" action="{{ Route('business.dte.product.select') }}">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
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
