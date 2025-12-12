<div class="overflow-x-auto">
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-900 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">Código</th>
                <th scope="col" class="px-6 py-3">Producto</th>
                <th scope="col" class="px-6 py-3">Stock Actual</th>
                <th scope="col" class="px-6 py-3">Stock Mínimo</th>
                <th scope="col" class="px-6 py-3">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stocks as $stock)
                <tr class="bg-white border-b dark:bg-gray-950 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-900">
                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                        {{ $stock->codigo }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $stock->descripcion }}
                    </td>
                    <td class="px-6 py-4 font-semibold">
                        {{ number_format($stock->stockActual, 2) }}
                    </td>
                    <td class="px-6 py-4">
                        {{ number_format($stock->stockMinimo, 2) }}
                    </td>
                    <td class="px-6 py-4">
                        @if($stock->estado_stock === 'disponible')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                <span class="w-2 h-2 mr-1 bg-green-500 rounded-full"></span>
                                Disponible
                            </span>
                        @elseif($stock->estado_stock === 'por_agotarse')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                <span class="w-2 h-2 mr-1 bg-yellow-500 rounded-full"></span>
                                Por Agotarse
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                <span class="w-2 h-2 mr-1 bg-red-500 rounded-full"></span>
                                Agotado
                            </span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                        No hay productos en stock
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
