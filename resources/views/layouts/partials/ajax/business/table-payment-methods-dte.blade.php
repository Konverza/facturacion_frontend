@if (isset($dte['metodos_pago']) && count($dte['metodos_pago']) > 0)
    @foreach ($dte['metodos_pago'] as $metodo_pago)
        <x-tr :last="$loop->last">
            <x-td>{{ $metodo_pago['forma_pago'] }}</x-td>
            <x-td>${{ $metodo_pago['monto'] }}</x-td>
            <x-td>{{ $metodo_pago['numero_documento'] }}</x-td>
            <x-td>{{ $metodo_pago['plazo'] }}</x-td>
            <x-td>{{ $metodo_pago['periodo'] }}</x-td>
            <x-td :last="true">
                <x-button type="button" icon="trash" size="small" typeButton="danger" text="Eliminar"
                    class="btn-delete"
                    data-action="{{ Route('business.dte.payment-method.delete', $metodo_pago['id']) }}" />
            </x-td>
        </x-tr>
    @endforeach
@else
    <x-tr :last="true">
        <x-td colspan="6" class="text-center" :last="true">No hay formas de pago</x-td>
    </x-tr>
@endif
