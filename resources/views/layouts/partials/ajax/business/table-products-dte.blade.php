<x-table :datatable="false">
    <x-slot name="thead">
        <x-tr>
            <x-th>Unidad de medida</x-th>
            <x-th>Descripción</x-th>
            <x-th>Cantidad</x-th>
            <x-th>Precio</x-th>
            <x-th>Descuento por item</x-th>
            <x-th>Venta gravada</x-th>
            <x-th>Venta exenta</x-th>
            <x-th>Venta no sujeta</x-th>
            <x-th :last="true">Acciones</x-th>
        </x-tr>
        <x-slot name="tbody">
            @if (isset($dte['products']) && count($dte['products']) > 0)
                @foreach ($dte['products'] as $product)
                    <x-tr>
                        <x-td>{{ $product['unidad_medida'] }}</x-td>
                        <x-td>
                            <div class="flex flex-col gap-1">
                                {{ $product['descripcion'] }}
                                @if (isset($product['documento_relacionado']) && $product['documento_relacionado'] !== null)
                                    <span class="text-xs text-gray-500">
                                        ({{ $product['documento_relacionado'] }})
                                    </span>
                                @endif
                            </div>
                        </x-td>
                        <x-td>{{ $product['cantidad'] }}</x-td>
                        <x-td>
                            @if ($dte['type'] !== '01')
                                ${{ number_format($product['precio_sin_tributos'], 8) }}
                            @else
                                ${{ number_format($product['precio'], 8) }}
                            @endif
                        </x-td>
                        <x-td>${{ number_format($product['descuento'], 8) }}</x-td>
                        <x-td>${{ number_format($product['ventas_gravadas'], 8) }}</x-td>
                        <x-td>${{ number_format($product['ventas_exentas'], 8) }}</x-td>
                        <x-td>${{ number_format($product['ventas_no_sujetas'], 8) }}</x-td>
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
                        Subtotal
                        <span>
                            ${{ number_format($dte['subtotal'] ?? 0, 2) }}
                        </span>
                    </div>
                </x-td>
            </x-tr>


            @if ($dte['type'] !== '01' && isset($dte['total_ventas_gravadas']) && $dte['total_ventas_gravadas'] > 0)
                <x-tr>
                    <x-td colspan="9" :last="true">
                        <div class="flex items-center justify-end gap-4 text-end">
                            Impuesto al valor agregado (13%)
                            <span>
                                ${{ number_format(round($dte['iva'] ?? 0, 2), 2) }}
                            </span>
                        </div>
                    </x-td>
                </x-tr>
            @endif

            @if (isset($dte['turismo_por_alojamiento']) && $dte['turismo_por_alojamiento'] > 0)
                <x-tr>
                    <x-td colspan="9" :last="true">
                        <div class="flex items-center justify-end gap-4 text-end">
                            Turismo por alojamiento
                            <span>
                                ${{ number_format($dte['turismo_por_alojamiento'], 2) }}
                            </span>
                        </div>
                    </x-td>
                </x-tr>
            @endif

            @if (isset($dte['turismo_salida_pais_via_aerea']) && $dte['turismo_salida_pais_via_aerea'] > 0)
                <x-tr>
                    <x-td colspan="9" :last="true">
                        <div class="flex items-center justify-end gap-4 text-end">
                            Turismo salida del país por vía aérea
                            <span>
                                ${{ number_format($dte['turismo_salida_pais_via_aerea'], 2) }}
                            </span>
                        </div>
                    </x-td>
                </x-tr>
            @endif

            @if (isset($dte['fovial']) && $dte['fovial'] > 0)
                <x-tr>
                    <x-td colspan="9" :last="true">
                        <div class="flex items-center justify-end gap-4 text-end">
                            FOVIAL ($0.20 por galón de combustible)
                            <span>
                                ${{ number_format($dte['fovial'], 2) }}
                            </span>
                        </div>
                    </x-td>
                </x-tr>
            @endif

            @if (isset($dte['contrans']) && $dte['contrans'] > 0)
                <x-tr>
                    <x-td colspan="9" :last="true">
                        <div class="flex items-center justify-end gap-4 text-end">
                            COTRANS ($0.10 por galón de combustible)
                            <span>
                                ${{ number_format($dte['contrans'], 2) }}
                            </span>
                        </div>
                    </x-td>
                </x-tr>
            @endif

            @if (isset($dte['bebidas_alcoholicas']) && $dte['bebidas_alcoholicas'] > 0)
                <x-tr>
                    <x-td colspan="9" :last="true">
                        <div class="flex items-center justify-end gap-4 text-end">
                            Impuesto ad-valorem por diferencial de precio de Bebidas Alcohólicas
                            (8%)
                            <span>
                                ${{ number_format($dte['bebidas_alcoholicas'], 2) }}
                            </span>
                        </div>
                    </x-td>
                </x-tr>
            @endif

            @if (isset($dte['tabaco_cigarillos']) && $dte['tabaco_cigarillos'] > 0)
                <x-tr>
                    <x-td colspan="9" :last="true">
                        <div class="flex items-center justify-end gap-4 text-end">
                            Impuesto ad-valorem por diferencial de precio al tabaco cigarrillos
                            (39%)
                            <span>
                                ${{ number_format($dte['tabaco_cigarillos'], 2) }}
                            </span>
                        </div>
                    </x-td>
                </x-tr>
            @endif

            @if (isset($dte['tabaco_cigarros']) && $dte['tabaco_cigarros'] > 0)
                <x-tr>
                    <x-td colspan="9" :last="true">
                        <div class="flex items-center justify-end gap-4 text-end">
                            Impuesto ad-valorem por diferencial de precio al tabaco cigarros
                            (100%)
                            <span>
                                ${{ number_format($dte['tabaco_cigarros'], 2) }}
                            </span>
                        </div>
                    </x-td>
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

            @if ($dte['type'] !== '04')
            <x-tr>
                <x-td colspan="9" :last="true">
                    <div class="flex items-center justify-end">
                        <div class="me-10">
                            <x-input type="checkbox" label="¿Retener IVA?" name="retener_iva"
                                data-action="{{ Route('business.dte.product.withhold') }}" id="retener_iva"
                                :checked="isset($dte['retener_iva']) && $dte['retener_iva'] === 'active'" class="retener" data-type="iva" />
                        </div>
                        <div class="flex items-center justify-end gap-4 text-end">
                            Retención IVA (1%)
                            <span class="font-semibold text-red-500">
                                @if (isset($dte['retener_iva']) && $dte['retener_iva'] === 'active')
                                    - ${{ number_format($dte['total_iva_retenido'], 2) }}
                                @else
                                    $0.00
                                @endif
                            </span>
                        </div>
                    </div>
                </x-td>
            </x-tr>
            @endif

            @if ($dte['type'] === '03' || $dte['type'] === '05')
                <x-tr>
                    <x-td colspan="9" :last="true">
                        <div class="flex items-center justify-end">
                            <div class="me-10">
                                <x-input type="checkbox" label="¿Percibir IVA?" name="percibir_iva"
                                    data-action="{{ Route('business.dte.product.withhold') }}" id="percibir_iva"
                                    :checked="isset($dte['percibir_iva']) && $dte['percibir_iva'] === 'active'" class="retener" data-type="percibir_iva" />
                            </div>
                            <div class="flex items-center justify-end gap-4 text-end">
                                Percibir IVA (1%)
                                <span class="font-semibold text-green-500">
                                    @if (isset($dte['percibir_iva']) && $dte['percibir_iva'] === 'active')
                                        + ${{ number_format($dte['total_iva_retenido'], 2) }}
                                    @else
                                        $0.00
                                    @endif
                                </span>
                            </div>
                        </div>
                    </x-td>
                </x-tr>
            @endif

            @if ($dte['type'] !== '04')
            <x-tr>
                <x-td colspan="9" :last="true">
                    <div class="flex items-center justify-end">
                        <div class="me-10">
                            <x-input type="checkbox" label="¿Retener renta?" name="retener_renta"
                                data-action="{{ Route('business.dte.product.withhold') }}" id="retener_renta"
                                class="retener" data-type="renta" :checked="isset($dte['retener_renta']) && $dte['retener_renta'] === 'active'" />
                        </div>
                        <div class="flex items-center justify-end gap-4 text-end">
                            Retención renta
                            <span class="font-semibold text-red-500">
                                @if (isset($dte['retener_renta']) && $dte['retener_renta'] === 'active')
                                    - ${{ number_format($dte['isr'], 2) }}
                                @else
                                    $0.00
                                @endif
                            </span>
                        </div>
                    </div>
                </x-td>
            </x-tr>
            @endif

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
@if (isset($dte['remove_discounts']) && !$dte['remove_discounts'])
    <div class="mt-4 flex flex-col justify-between gap-4 sm:flex-row">
        <x-button type="button" text="Agregar descuento" typeButton="info" icon="plus" data-target="#add-discount"
            class="show-modal w-full sm:w-auto" />
        <x-button type="button" text="Eliminar descuento" typeButton="danger" icon="trash"
            data-target="#delete-discount" class="show-modal delete-discounts w-full sm:w-auto"
            data-action="{{ Route('business.dte.product.remove-discounts') }}" />
    </div>
@endif
