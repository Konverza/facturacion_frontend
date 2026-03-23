<div class="flex flex-col gap-4 p-4">
    <span class="flex items-center gap-1 text-base font-semibold text-gray-500 dark:text-gray-300">
        <x-icon icon="info-circle" class="size-5" />
        Criterios de consulta
    </span>

    <div class="flex flex-col gap-4 sm:flex-row">
        <div class="flex-1">
            <x-input type="text" label="Buscar" name="search_dte_cxc" placeholder="Código, nombre o documento"
                id="search-dte-cxc" wire:model.live.debounce.500ms="search" />
        </div>
        <div class="flex-1">
            <x-select :options="[
                '' => 'Todos',
                '01' => 'Factura',
                '03' => 'CCF',
                '04' => 'Nota de remisión',
                '05' => 'Nota de crédito',
                '06' => 'Nota de débito',
                '07' => 'Comprobante de retención',
                '08' => 'Comprobante de liquidación',
                '09' => 'Documento contable de liquidación',
                '11' => 'Factura de exportación',
                '14' => 'Factura sujeto excluido',
            ]" name="tipo_documento_cxc" id="tipo-documento-cxc" label="Tipo de documento" :search="false"
                wire:model.live.debounce.500ms="tipo_documento" :value="$tipo_documento" :selected="$tipo_documento" />
        </div>
    </div>

    <div class="flex flex-col gap-4 sm:flex-row">
        <div class="flex-1">
            <x-input type="date" id="emitido-desde-cxc" name="emitido_desde_cxc" label="Fecha emitido desde"
                wire:model.live.debounce.500ms="emitido_desde" />
        </div>
        <div class="flex-1">
            <x-input type="date" label="Fecha emitido hasta" name="emitido_hasta_cxc" id="emitido-hasta-cxc"
                wire:model.live.debounce.500ms="emitido_hasta" />
        </div>
    </div>

    <div class="flex justify-center mt-2">
        <x-button type="button" wire:click="clearFilters" typeButton="info" text="Limpiar filtros" />
    </div>

    <span class="mb-2 flex items-center gap-1 text-base font-semibold text-gray-500 dark:text-gray-300">
        <x-icon icon="search" class="size-5" />
        Resultados de búsqueda
    </span>

    <div class="relative">
        <div wire:loading.delay wire:target="search,tipo_documento,emitido_desde,emitido_hasta,page,perPage,clearFilters"
            class="absolute inset-0 mt-8 bg-white bg-opacity-70 z-50 flex items-center justify-center dark:bg-gray-900/70">
            <div class="text-center">
                <svg class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                </svg>
                <p class="text-gray-700 text-sm dark:text-gray-200">Cargando datos...</p>
            </div>
        </div>

        <x-table :datatable="false">
            <x-slot name="thead">
                <x-tr>
                    <x-th>Tipo</x-th>
                    <x-th>Fecha de emisión</x-th>
                    <x-th>Código de generación</x-th>
                    <x-th :last="true"></x-th>
                </x-tr>
            </x-slot>
            <x-slot name="tbody">
                @forelse ($dtes as $dte)
                    <x-tr>
                        <x-td class="text-xs">{{ $dte['tipo_dte'] ?? 'N/A' }}</x-td>
                        <x-td class="text-xs">
                            @if (!empty($dte['fhProcesamiento']))
                                {{ \Carbon\Carbon::parse($dte['fhProcesamiento'])->format('d/m/Y') }}
                            @else
                                N/A
                            @endif
                        </x-td>
                        <x-td class="text-xs">{{ $dte['codGeneracion'] ?? 'N/A' }}</x-td>
                        <x-td :last="true">
                            <x-button type="button" icon="arrow-next" size="small" typeButton="secondary"
                                text="Seleccionar" data-cod="{{ $dte['codGeneracion'] ?? '' }}"
                                class="btn-select-cxc-dte" />
                        </x-td>
                    </x-tr>
                @empty
                    <x-tr>
                        <x-td colspan="4" class="text-center">No hay resultados</x-td>
                    </x-tr>
                @endforelse
            </x-slot>
        </x-table>
    </div>

    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mt-2">
        <div>
            <label class="text-sm">
                Mostrar
                <select wire:model.live.debounce.500ms="perPage"
                    class="ml-1 rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-200 text-sm">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                </select>
                registros por página
            </label>
        </div>
        <div class="flex items-center gap-2 text-sm">
            Página {{ $page }} de {{ $total_pages }}
            <button wire:click="$set('page', {{ max(1, $page - 1) }})"
                class="px-3 py-1 border rounded text-sm hover:bg-gray-100 {{ $page <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                @if ($page <= 1) disabled @endif>
                Anterior
            </button>
            <button wire:click="$set('page', {{ min($total_pages, $page + 1) }})"
                class="px-3 py-1 border rounded text-sm hover:bg-gray-100 {{ $page >= $total_pages ? 'opacity-50 cursor-not-allowed' : '' }}"
                @if ($page >= $total_pages) disabled @endif>
                Siguiente
            </button>
        </div>
    </div>

    @push('scripts')
        <script>
            $("#tipo-documento-cxc").on("Changed", function() {
                @this.set('tipo_documento', $(this).val());
            });
        </script>
    @endpush
</div>
