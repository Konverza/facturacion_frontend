<!-- Sección forma de pago -->
@php
    $plazos = ['01' => 'Días', '02' => 'Meses', '03' => 'Años'];
@endphp
<div class="mt-4 border-t border-gray-300 px-4 pt-4 dark:border-gray-800">
    <h2 class="flex items-center gap-1 text-xl font-semibold text-primary-500 dark:text-primary-300">
        <x-icon icon="info-circle" class="size-5" />
        Forma de pago
    </h2>
    <div class="mt-2 flex flex-col gap-4 md:flex-row">
        <input type="hidden" id="payment_method_id" value="">
        <div class="flex-[2]" id="input-forma-pago">
            <x-select id="forma_pago" name="forma_pago" label="Forma de pago" :options="$metodos_pago" />
        </div>
        <div class="flex-1">
            <x-input type="number" label="Monto" name="monto" id="monto_total"
                value="{{ isset($dte['monto_pendiente']) && $dte['monto_pendiente'] > 0 ? round($dte['monto_pendiente'], 2) : 0 }}"
                step="0.01" icon="currency-dollar" placeholder="0.00" />
        </div>
        <div class="flex-[2]">
            <x-input type="text" label="Número de documento" id="numero_documento" name="numero_documento_pago"
                placeholder="Ingresa el número del documento" value="0" />
        </div>
        <div class="hidden flex-1" id="input-plazo">
            <x-select label="Plazo" id="plazo" name="plazo" :options="$plazos" :search="false" />
        </div>
        <div class="hidden flex-1" id="input-periodo">
            <x-input type="number" label="Período" name="periodo" id="periodo" placeholder="0" />
        </div>
        <div class="flex items-end gap-2 md:mt-6">
            <x-button type="button" icon="plus" typeButton="success" id="btn-add-forma-pago"
                data-action="{{ Route('business.dte.payment-method.store') }}" text="Agregar forma de pago" />
            <x-button type="button" icon="arrows-right-left" typeButton="primary" id="btn-sync-monto-pendiente"
                data-action="{{ Route('business.dte.payment-method.sync-last') }}" text="Actualizar Monto" />
            <x-button type="button" icon="x" typeButton="warning" id="btn-cancel-edit-forma-pago"
                class="hidden" text="Cancelar" />
        </div>
    </div>
    <div class="mt-4">
        @if ($number === '15')
            <div class="mb-4 border-l-4 border-blue-500 bg-blue-100 p-4 text-sm text-blue-700 dark:bg-blue-950/50 dark:text-blue-300"
                role="alert">
                <div class="flex justify-start gap-2">
                    <x-icon icon="info-circle" class="h-5 w-5" />
                    Solo debe ingresar una forma de pago si la donación recibida es monetaria. Si se reciben bienes o servicios, no completar
                </div>
            </div>
        @endif
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
                            <x-td>{{ $formas_pago[$metodo_pago['forma_pago']] ?? $metodo_pago['forma_pago'] }}</x-td>
                            <x-td>${{ $metodo_pago['monto'] }}</x-td>
                            <x-td>{{ $metodo_pago['numero_documento'] }}</x-td>
                            <x-td>{{ $plazos[$metodo_pago['plazo']] ?? $metodo_pago['plazo'] }}</x-td>
                            <x-td>{{ $metodo_pago['periodo'] }}</x-td>
                            <x-td :last="true">
                                <div class="flex items-center gap-2">
                                    <x-button type="button" icon="pencil" size="small" typeButton="info"
                                        text="Editar" class="btn-edit-forma-pago"
                                        data-id="{{ $metodo_pago['id'] }}"
                                        data-forma-pago="{{ $metodo_pago['forma_pago'] }}"
                                        data-monto="{{ $metodo_pago['monto'] }}"
                                        data-numero-documento="{{ $metodo_pago['numero_documento'] }}"
                                        data-plazo="{{ $metodo_pago['plazo'] }}"
                                        data-periodo="{{ $metodo_pago['periodo'] }}" />

                                    <x-button type="button" icon="trash" size="small" typeButton="danger"
                                        text="Eliminar" class="btn-delete"
                                        data-action="{{ Route('business.dte.payment-method.delete', $metodo_pago['id']) }}" />
                                </div>
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
