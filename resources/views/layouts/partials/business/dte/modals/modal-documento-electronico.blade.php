<!-- Modal add documento electronico -->
<div id="add-documento-electronico" tabindex="-1" aria-hidden="true"
    class="fixed left-0 right-0 top-0 z-[100] hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
    <div class="relative m-4 mb-8 max-h-full w-full max-w-[950px]">
        <!-- Modal content -->
        <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
            <div class="flex flex-col">
                <!-- Modal header -->
                <div
                    class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Documento electrónico
                    </h3>
                    <button type="button"
                        class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                        data-target="#add-documento-electronico">
                        <x-icon icon="x" class="h-5 w-5" />
                    </button>
                </div>
                <!-- Modal body -->
                <div class="flex flex-col gap-4 p-4">
                    <span class="flex items-center gap-1 text-base font-semibold text-gray-500 dark:text-gray-300">
                        <x-icon icon="info-circle" class="size-5" />
                        Criterios de consulta
                    </span>
                    <div class="flex flex-col gap-4 sm:flex-row">
                        <div class="flex-1">
                            <x-input type="text" label="Número de documento" name="nit_receptor" readonly
                                placeholder="Ingresar el número de documento" id="nit-receptor"
                                value="{{ old('numero_documento', isset($dte['customer']) ? $dte['customer']['numDocumento'] : '') }}" />
                        </div>
                        <div class="flex-1">
                            <x-select :options="[
                                'Comprobante de crédito fiscal' => 'Comprobante de crédito fiscal',
                                'Comprobante de retención' => 'Comprobante de retención',
                            ]" name="tipo_documento" id="tipo_documento"
                                label="Tipo de documento" :search="false" />
                        </div>
                    </div>
                    <div class="flex flex-col gap-4 sm:flex-row">
                        <div class="flex-1">
                            <x-input type="date" id="emitido-desde" name="emitido-desde"
                                label="Fecha emitido desde" />
                        </div>
                        <div class="flex-1">
                            <x-input type="date" label="Fecha emitido hasta" name="emitido-hasta"
                                id="emitido-hasta" />
                        </div>
                    </div>
                </div>
                <div class="px-4 pb-4">
                    <span class="mb-2 flex items-center gap-1 text-base font-semibold text-gray-500 dark:text-gray-300">
                        <x-icon icon="info-circle" class="size-5" />
                        Resultados de la consulta
                    </span>
                    <div id="container-table-dtes">
                        <x-table id="table-dtes">
                            <x-slot name="thead">
                                <x-tr>
                                    <x-th>Tipo</x-th>
                                    <x-th>NIT</x-th>
                                    <x-th>Fecha de emisión</x-th>
                                    <x-th>Código de generación</x-th>
                                    <x-th></x-th>
                                </x-tr>
                            </x-slot>
                            <x-slot name="tbody">
                                @foreach ($dtes as $dte)
                                    <x-tr>
                                        <x-td class="text-xs">
                                            @if ($dte['tipo_dte'] == '03')
                                                Comprobante de crédito fiscal
                                            @elseif($dte['tipo_dte'] == '07')
                                                Comprobante de retención
                                            @endif
                                        </x-td>
                                        <x-td class="text-xs">
                                            @php
                                                $documento = json_decode($dte['documento'], true);
                                            @endphp
                                            @if (isset($documento['receptor']['numDocumento']))
                                                {{ $documento['receptor']['numDocumento'] }}
                                            @elseif(isset($documento['receptor']['nit']))
                                                {{ $documento['receptor']['nit'] }}
                                            @elseif(isset($documento['sujetoExcluido']))
                                                {{ $documento['sujetoExcluido']['numDocumento'] }}
                                            @endif
                                        </x-td>
                                        <x-td class="text-xs">
                                            {{ \Carbon\Carbon::parse($dte['fhProcesamiento'])->format('d/m/Y') }}
                                        </x-td>
                                        <x-td class="text-xs">
                                            {{ $dte['codGeneracion'] }}
                                        </x-td>
                                        <x-td :last="true">
                                            <x-button type="button" icon="arrow-next" size="small"
                                                data-url="{{ route('business.dte.related-documents.store-electric') }}"
                                                typeButton="secondary" text="Seleccionar"
                                                data-cod="{{ $dte['codGeneracion'] }}"
                                                class="btn-selected-document-electric" />
                                        </x-td>
                                    </x-tr>
                                @endforeach
                            </x-slot>
                        </x-table>
                    </div>
                </div>
                <!-- Modal footer -->
                <div
                    class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                    <x-button type="button" class="hide-modal" text="Cancelar" icon="x" typeButton="secondary"
                        data-target="#add-documento-electronico" />
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Modal add add documento electronico -->
