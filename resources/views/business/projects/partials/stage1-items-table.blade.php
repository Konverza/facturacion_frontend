@php
    $items = $comparison['items'] ?? [];
    $providerNames = $comparison['provider_names'] ?? [];
    $totalBestCost = (float) ($comparison['total_best_cost'] ?? 0);
@endphp

<x-table :datatable="false">
    <x-slot name="thead">
        <x-tr>
            <x-th>#</x-th>
            <x-th>Cantidad</x-th>
            <x-th>Unidad de medida</x-th>
            <x-th>Material (Descripción)</x-th>
            <x-th>Código</x-th>
            @foreach ($providerNames as $providerName)
                <x-th>{{ $providerName }}</x-th>
            @endforeach
            <x-th>Precio más bajo</x-th>
            <x-th>Proveedor más bajo</x-th>
            <x-th :last="true">Acciones</x-th>
        </x-tr>
    </x-slot>

    <x-slot name="tbody">
        @forelse ($items as $item)
            <x-tr :last="$loop->last">
                <x-td>{{ $loop->iteration }}</x-td>
                <x-td>{{ number_format((float) ($item['cantidad'] ?? 0), 2) }}</x-td>
                <x-td>{{ $item['unidad_medida'] ?? '-' }}</x-td>
                <x-td>{{ $item['descripcion'] ?? '-' }}</x-td>
                <x-td>{{ $item['codigo'] ?? '-' }}</x-td>

                @foreach ($providerNames as $providerName)
                    @php
                        $supplierMap = $item['supplier_map'] ?? [];
                        $cost = $supplierMap[$providerName] ?? null;
                    @endphp
                    <x-td>
                        {{ $cost !== null ? '$' . number_format((float) $cost, 4) : '-' }}
                    </x-td>
                @endforeach

                <x-td>${{ number_format((float) ($item['best_unit_cost'] ?? 0), 4) }}</x-td>
                <x-td>{{ $item['best_supplier'] ?? 'Sin proveedor' }}</x-td>
                <x-td :last="true">
                    <div class="flex flex-col gap-2">
                        @if (($item['source'] ?? 'manual') !== 'catalog' || !($item['lock_catalog_pair'] ?? false))
                            <form method="POST"
                                action="{{ Route('business.projects.items.add-supplier-cost', ['project' => $project->id, 'itemId' => $item['id']]) }}"
                                class="grid grid-cols-1 gap-2">
                                @csrf
                                <input type="text" name="supplier_name" placeholder="Proveedor"
                                    class="rounded-lg border border-gray-300 p-2 text-xs dark:border-gray-700 dark:bg-gray-900"
                                    required />
                                <input type="number" name="unit_cost" placeholder="Costo unitario"
                                    class="rounded-lg border border-gray-300 p-2 text-xs dark:border-gray-700 dark:bg-gray-900"
                                    min="0" step="0.0001" required />
                                <x-button type="submit" text="Agregar costo" icon="plus" typeButton="secondary" size="small" />
                            </form>
                        @else
                            <div class="rounded-lg border border-blue-300 bg-blue-50 px-2 py-2 text-xs text-blue-700 dark:border-blue-800 dark:bg-blue-950/30 dark:text-blue-300">
                                Par costo/precio fijo (no editable)
                            </div>
                        @endif

                        <form method="POST"
                            action="{{ Route('business.projects.items.delete', ['project' => $project->id, 'itemId' => $item['id']]) }}">
                            @csrf
                            <x-button type="submit" text="Eliminar" icon="trash" typeButton="danger" size="small" />
                        </form>
                    </div>
                </x-td>
            </x-tr>
        @empty
            <x-tr>
                <x-td colspan="{{ 8 + count($providerNames) }}" class="py-8 text-center text-gray-500">No hay items en el proyecto.</x-td>
            </x-tr>
        @endforelse
    </x-slot>
</x-table>

<div class="mt-4 rounded-lg border border-dashed border-emerald-500 bg-emerald-50 p-4 text-sm text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950/30 dark:text-emerald-300">
    <span class="font-semibold">Costo total con mejor proveedor:</span>
    ${{ number_format($totalBestCost, 4) }}
</div>
