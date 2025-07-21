<x-table :datatable="false">
    <x-slot name="thead">
        <x-tr>
            <x-th>Unidad de medida</x-th>
            <x-th>Descripción</x-th>
            <x-th>Cantidad</x-th>
            <x-th>Depreciación</x-th>
            <x-th>Valor Unitario</x-th>
            <x-th>Valor Donado</x-th>
            <x-th :last="true">Acciones</x-th>
        </x-tr>
        <x-slot name="tbody">
            @if (isset($dte['products']) && count($dte['products']) > 0)
                @foreach ($dte['products'] as $product)
                    <x-tr>
                        <x-td>{{ $product['unidad_medida'] }}</x-td>
                        <x-td>{{ $product['descripcion'] }}</x-td>
                        <x-td>{{ $product['cantidad'] }}</x-td>
                        <x-td>${{ number_format($product['depreciacion'], 2) }}</x-td>
                        <x-td>${{ number_format($product['valor_unitario'], 2) }}</x-td>
                        <x-td>${{ number_format($product['valor_donado'], 2) }}</x-td>
                        <x-td :last="true">
                            <x-button type="button" icon="trash" size="small"
                                data-action="{{ Route('business.dte.donation.delete', $product['id']) }}"
                                typeButton="danger" text="Eliminar" class="btn-delete" />
                        </x-td>
                    </x-tr>
                @endforeach
            @else
                <x-tr>
                    <x-td :last="true" colspan="9" class="text-center">No hay productos</x-td>
                </x-tr>
            @endif

            <x-tr :last="true">
                <x-td colspan="9" :last="true">
                    <div class="flex items-center justify-end gap-4 text-end">
                        Total de la Donación
                        <span>
                            ${{ number_format($dte['total'] ?? 0, 2) }}
                        </span>
                    </div>
                </x-td>
            </x-tr>
        </x-slot>
    </x-slot>
</x-table>
