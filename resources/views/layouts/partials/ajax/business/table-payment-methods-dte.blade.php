@php
    $plazos = ['01' => 'Días', '02' => 'Meses', '03' => 'Años'];
@endphp
@if (isset($dte['metodos_pago']) && count($dte['metodos_pago']) > 0)
    @foreach ($dte['metodos_pago'] as $metodo_pago)
        <x-tr :last="$loop->last">
            <x-td>{{ $formas_pago[$metodo_pago['forma_pago']] ?? $metodo_pago['forma_pago'] }}</x-td>
            <x-td>${{ $metodo_pago['monto'] }}</x-td>
            <x-td>{{ $metodo_pago['numero_documento'] }}</x-td>
            <x-td>{{ $plazos[$metodo_pago['plazo']] ?? $metodo_pago['plazo'] }}</x-td>
            <x-td>{{ $metodo_pago['periodo'] }}</x-td>
            <x-td :last="true">
                <div class="flex items-center gap-2">
                    <x-button type="button" icon="pencil" size="small" typeButton="info" text="Editar"
                        class="btn-edit-forma-pago"
                        data-id="{{ $metodo_pago['id'] }}"
                        data-forma-pago="{{ $metodo_pago['forma_pago'] }}"
                        data-monto="{{ $metodo_pago['monto'] }}"
                        data-numero-documento="{{ $metodo_pago['numero_documento'] }}"
                        data-plazo="{{ $metodo_pago['plazo'] }}"
                        data-periodo="{{ $metodo_pago['periodo'] }}" />

                    <x-button type="button" icon="trash" size="small" typeButton="danger" text="Eliminar"
                        class="btn-delete"
                        data-action="{{ Route('business.dte.payment-method.delete', $metodo_pago['id']) }}" />
                </div>
            </x-td>
        </x-tr>
    @endforeach
@else
    <x-tr :last="true">
        <x-td colspan="6" class="text-center" :last="true">No hay formas de pago</x-td>
    </x-tr>
@endif
