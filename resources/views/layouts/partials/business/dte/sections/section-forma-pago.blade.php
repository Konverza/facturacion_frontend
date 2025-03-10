<!-- Sección forma de pago -->
<div class="mt-4 border-t border-gray-300 px-4 pt-4 dark:border-gray-800">
    <h2 class="flex items-center gap-1 text-xl font-semibold text-primary-500 dark:text-primary-300">
        <x-icon icon="info-circle" class="size-5" />
        Forma de pago
    </h2>
    <div class="mt-2 flex flex-col gap-4 md:flex-row">
        <div class="flex-[2]" id="input-forma-pago">
            <x-select id="forma_pago" name="forma_pago" label="Forma de pago" :options="$metodos_pago" />
        </div>
        <div class="flex-1">
            <x-input type="number" label="Monto" name="monto" id="monto_total"
                value="{{ isset($dte['monto_pendiente']) ? round($dte['monto_pendiente'], 2) : 0 }}" step="0.01"
                icon="currency-dollar" placeholder="0.00" />
        </div>
        <div class="flex-[2]">
            <x-input type="text" label="Número de documento" id="numero_documento" name="numero_documento_pago"
                placeholder="Ingresa el número del documento" value="0" />
        </div>
        <div class="hidden flex-1" id="input-plazo">
            <x-select label="Plazo" id="plazo" name="plazo" :options="['Días' => 'Días', 'Meses' => 'Meses', 'Años' => 'Años']" :search="false" />
        </div>
        <div class="hidden flex-1" id="input-periodo">
            <x-input type="number" label="Período" name="periodo" id="periodo" placeholder="0" />
        </div>
        <x-button type="button" onlyIcon icon="plus" typeButton="success" id="btn-add-forma-pago" class="md:mt-6"
            data-action="{{ Route('business.dte.payment-method.store') }}" />
    </div>
    <div class="mt-4">
        <x-table :datatable="false">
            <x-slot name="thead">
                <x-tr>
                    <x-th>Forma de pago</x-th>
                    <x-th>Monto</x-th>
                    <x-th>Número de documento</x-th>
                    <x-th>Plazo</x-th>
                    <x-th>Período</x-th>
                    <x-th :last="true">Acciones</x-th>
                </x-tr>
            </x-slot>
            <x-slot name="tbody" id="table-formas-pago">
                @if (isset($dte['metodos_pago']) && count($dte['metodos_pago']) > 0)
                    @foreach ($dte['metodos_pago'] as $metodo_pago)
                        <x-tr :last="$loop->last">
                            <x-td>{{ $metodo_pago['forma_pago'] }}</x-td>
                            <x-td>${{ $metodo_pago['monto'] }}</x-td>
                            <x-td>{{ $metodo_pago['numero_documento'] }}</x-td>
                            <x-td>{{ $metodo_pago['plazo'] }}</x-td>
                            <x-td>{{ $metodo_pago['periodo'] }}</x-td>
                            <x-td :last="true">
                                <x-button type="button" icon="trash" size="small" typeButton="danger"
                                    text="Eliminar" class="btn-delete"
                                    data-action="{{ Route('business.dte.payment-method.delete', $metodo_pago['id']) }}" />
                            </x-td>
                        </x-tr>
                    @endforeach
                @else
                    <x-tr :last="true">
                        <x-td colspan="6" class="text-center" :last="true">
                            No hay formas de pago
                        </x-td>
                    </x-tr>
                @endif
            </x-slot>
        </x-table>
    </div>
</div>
<!-- End Sección forma de pago -->
