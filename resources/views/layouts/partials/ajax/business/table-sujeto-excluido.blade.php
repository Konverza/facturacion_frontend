<x-table :datatable="false">
    <x-slot name="thead">
        <x-tr>
            <x-th>Unidad de medida</x-th>
            <x-th>Descripción</x-th>
            <x-th>Cantidad</x-th>
            <x-th>Precio</x-th>
            <x-th>Descuento por item</x-th>
            <x-th>Ventas</x-th>
            <x-th :last="true">Acciones</x-th>
        </x-tr>
        <x-slot name="tbody">
            @if (isset($dte['products']) && count($dte['products']) > 0)
                @foreach ($dte['products'] as $product)
                    <x-tr>
                        <x-td>{{ $product['unidad_medida'] }}</x-td>
                        <x-td>{{ $product['descripcion'] }}</x-td>
                        <x-td>{{ $product['cantidad'] }}</x-td>
                        <x-td>
                            ${{ number_format($product['precio_sin_tributos'], 2) }}
                        </x-td>
                        <x-td>${{ number_format($product['descuento'], 2) }}</x-td>
                        <x-td>${{ number_format($product['total'], 2) }}</x-td>
                        <x-td :last="true">
                            <x-button type="button" icon="trash" size="small"
                                data-action="{{ Route('business.dte.product.delete', $product['id']) }}"
                                typeButton="danger" text="Eliminar" class="btn-delete" />
                        </x-td>
                    </x-tr>
                @endforeach
            @else
                <x-tr>
                    <x-td :last="true" colspan="9" class="text-center">No hay productos</x-td>
                </x-tr>
            @endif

            <x-tr>
                <x-td colspan="9" :last="true">
                    <div class="flex items-center justify-end gap-4 text-end">
                        Monto total de la operación
                        <span>
                            ${{ number_format($dte['total'] ?? 0, 2) }}
                        </span>
                    </div>
                </x-td>
            </x-tr>

            <x-tr>
                <x-td colspan="9" :last="true">
                    <div class="flex items-center justify-end gap-4 text-end">
                        Retención renta
                         <span class="font-semibold text-red-500">
                            @if (isset($dte['isr']) && $dte['isr'] > 0)
                                - ${{ number_format($dte['isr'] ?? 0, 2) }}
                            @else
                                $0.00
                            @endif
                        </span>
                    </div>
                </x-td>
            </x-tr>

            <x-tr>
                <x-td colspan="9" :last="true">
                    <div class="flex items-center justify-end gap-4 text-end">
                        Descuento a operación
                        <span class="font-semibold text-red-500">
                            @if (isset($dte['total_descuentos']) && $dte['total_descuentos'] > 0)
                                - ${{ number_format($dte['total_descuentos'] ?? 0, 2) }}
                            @else
                                $0.00
                            @endif
                        </span>
                    </div>
                </x-td>
            </x-tr>

            <x-tr :last="true">
                <x-td colspan="9" :last="true">
                    <div class="flex items-center justify-end gap-4 text-end">
                        Total pagar
                        <span>
                            @if (isset($dte['total_pagar']))
                                ${{ number_format($dte['total_pagar'] ?? 0, 2) }}
                            @endif
                        </span>
                    </div>
                </x-td>
            </x-tr>

        </x-slot>
    </x-slot>
</x-table>
